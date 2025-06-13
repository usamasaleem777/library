<?php
include(__DIR__ . '/includes/header.php');
include(__DIR__ . '/config/db.php');

if (!$conn) {
    die("Database connection failed");
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 12;
    $offset = ($page - 1) * $limit;

    $filters = [
        'availability' => [],
        'format' => [],
        'genre' => [],
        'year_min' => 1900,
        'year_max' => (int)date('Y')
    ];

    if (isset($_GET['availability']) && is_array($_GET['availability'])) {
        $validAvailability = ['available', 'checked-out', 'e-resources'];
        $filters['availability'] = array_intersect($_GET['availability'], $validAvailability);
    }

    if (isset($_GET['format']) && is_array($_GET['format'])) {
        $validFormats = ['hardcover', 'paperback', 'ebook', 'audiobook', 'book'];
        $filters['format'] = array_intersect($_GET['format'], $validFormats);
    }

    if (isset($_GET['genre']) && is_array($_GET['genre'])) {
        $genreResult = $conn->query("SELECT DISTINCT genre FROM books");
        while ($row = $genreResult->fetch_assoc()) {
            $validGenres[] = $row['genre'];
        }
        $filters['genre'] = array_intersect($_GET['genre'], $validGenres ?? []);
    }

    if (isset($_GET['year_min']) && is_numeric($_GET['year_min'])) {
        $filters['year_min'] = max(1900, min((int)$_GET['year_min'], $filters['year_max']));
    }

    if (isset($_GET['year_max']) && is_numeric($_GET['year_max'])) {
        $filters['year_max'] = min((int)date('Y'), max((int)$_GET['year_max'], $filters['year_min']));
    }

    $sqlBase = "FROM books b LEFT JOIN book_metadata m ON b.id = m.book_id WHERE 1=1";
    $params = [];
    $types = '';

    if ($search !== '') {
        $sqlBase .= " AND (b.title LIKE CONCAT('%', ?, '%') OR b.author LIKE CONCAT('%', ?, '%') OR b.isbn LIKE CONCAT('%', ?, '%'))";
        $params = array_merge($params, [$search, $search, $search]);
        $types .= 'sss';
    }

    if (!empty($filters['availability'])) {
        $cond = [];
        foreach ($filters['availability'] as $a) {
            if ($a === 'available') $cond[] = "b.available = TRUE";
            elseif ($a === 'checked-out') $cond[] = "b.available = FALSE";
            elseif ($a === 'e-resources') $cond[] = "b.format IN ('ebook','audiobook')";
        }
        if ($cond) $sqlBase .= " AND (" . implode(" OR ", $cond) . ")";
    }

    if ($filters['format']) {
        $ph = implode(',', array_fill(0, count($filters['format']), '?'));
        $sqlBase .= " AND b.format IN ($ph)";
        $params = array_merge($params, $filters['format']);
        $types .= str_repeat('s', count($filters['format']));
    }

    if ($filters['genre']) {
        $ph = implode(',', array_fill(0, count($filters['genre']), '?'));
        $sqlBase .= " AND b.genre IN ($ph)";
        $params = array_merge($params, $filters['genre']);
        $types .= str_repeat('s', count($filters['genre']));
    }

    $sqlBase .= " AND b.publication_year BETWEEN ? AND ?";
    $params = array_merge($params, [$filters['year_min'], $filters['year_max']]);
    $types .= 'ii';

    $countSql = "SELECT COUNT(*) AS total $sqlBase";
    $countStmt = $conn->prepare($countSql);
    if (!$countStmt) die("Count prepare failed: " . $conn->error);
    if ($params) $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $totalBooks = $countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();

    $totalPages = ceil($totalBooks / $limit);

    $sort = $_GET['sort'] ?? 'title-asc';
    $validSorts = ['title-asc','title-desc','author-asc','author-desc','year-asc','year-desc','popular'];
    if (!in_array($sort, $validSorts)) $sort = 'title-asc';

    $orderBy = match($sort) {
        'title-desc' => 'b.title DESC',
        'author-asc' => 'b.author ASC',
        'author-desc' => 'b.author DESC',
        'year-asc' => 'b.publication_year ASC',
        'year-desc' => 'b.publication_year DESC',
        'popular' => 'COALESCE(m.checkouts,0) DESC, COALESCE(m.views,0) DESC',
        default => 'b.title ASC',
    };

    $sql = "SELECT b.*, 
                (SELECT COUNT(*) FROM wishlist w WHERE w.book_id=b.id) AS wishlist_count,
                m.views, m.checkouts 
            $sqlBase ORDER BY $orderBy LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Query prepare failed: " . $conn->error);

    $execParams = $params;
    $execTypes = $types;
    $execParams[] = $limit;
    $execParams[] = $offset;
    $execTypes .= 'ii';

    $stmt->bind_param($execTypes, ...$execParams);
    $stmt->execute();
    $result = $stmt->get_result();

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = [
            'id' => (int)$row['id'],
            'title' => htmlspecialchars($row['title']),
            'author' => htmlspecialchars($row['author']),
            'isbn' => htmlspecialchars($row['isbn']),
            'description' => htmlspecialchars($row['description']),
            'cover_image' => htmlspecialchars($row['cover_image']),
            'publication_year' => (int)$row['publication_year'],
            'genre' => htmlspecialchars($row['genre'] ?? ''),
            'format' => htmlspecialchars($row['format'] ?? ''),
            'available' => (bool)$row['available'],
            'wishlist_count' => (int)$row['wishlist_count'],
            'views' => (int)($row['views'] ?? 0),
            'checkouts' => (int)($row['checkouts'] ?? 0)
        ];
    }
    $stmt->close();

    function getCounts($conn, $field, $sqlBase, $params, $types) {
        $q = "SELECT $field AS val, COUNT(*) AS cnt $sqlBase GROUP BY $field";
        $st = $conn->prepare($q);
        if ($params) $st->bind_param($types, ...$params);
        $st->execute();
        $res = $st->get_result();
        $out = [];
        while ($r = $res->fetch_assoc()) {
            $out[$r['val']] = (int)$r['cnt'];
        }
        $st->close();
        return $out;
    }

    $filterCounts = [
        'availability' => [
            'available' => (int)$conn->query("SELECT COUNT(*) FROM books WHERE available=TRUE")->fetch_row()[0],
            'checked-out' => (int)$conn->query("SELECT COUNT(*) FROM books WHERE available=FALSE")->fetch_row()[0],
            'e-resources' => (int)$conn->query("SELECT COUNT(*) FROM books WHERE format IN ('ebook','audiobook')")->fetch_row()[0]
        ],
        'format' => getCounts($conn, 'format', $sqlBase, $params, $types),
        'genre' => getCounts($conn, 'genre', $sqlBase, $params, $types),
    ];

    // $response is available for internal use only
    $response = [
        'success' => true,
        'data' => [
            'books' => $books,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_books' => $totalBooks,
                'books_per_page' => $limit
            ],
            'filter_counts' => $filterCounts,
            'applied_filters' => $filters,
            'sort' => $sort,
            'search' => $search
        ]
    ];

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
} finally {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Catalog | Digital Library System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .breadcrumb {
            display: flex;
            padding: 15px 0;
            font-size: 0.9rem;
            color: #666;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .breadcrumb .separator {
            margin: 0 8px;
            color: #999;
        }

        .catalog-tools {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            flex-grow: 1;
            max-width: 600px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 2px 12px rgba(52, 152, 219, 0.2);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .filter-btn {
            background: white;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--secondary-color);
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .filter-btn:hover {
            background: #f5f5f5;
            border-color: #ccc;
        }

        .view-options {
            display: flex;
            background: white;
            border: 1px solid #ddd;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .view-option {
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.3s;
            border-right: 1px solid #eee;
        }

        .view-option:last-child {
            border-right: none;
        }

        .view-option.active {
            background: var(--primary-color);
            color: white;
        }

        .view-option:hover:not(.active) {
            background: #f5f5f5;
        }

        .catalog-content {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
        }

        .sidebar {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }

        .sidebar h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-color);
            color: var(--secondary-color);
        }

        .filter-section {
            margin-bottom: 25px;
        }

        .filter-section h4 {
            font-size: 1rem;
            margin-bottom: 12px;
            color: var(--secondary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .filter-section h4 i {
            transition: transform 0.3s;
        }

        .filter-section.collapsed h4 i {
            transform: rotate(-90deg);
        }

        .filter-section.collapsed .filter-options {
            display: none;
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .filter-option input {
            accent-color: var(--primary-color);
        }

        .filter-option label {
            cursor: pointer;
        }

        .filter-option .count {
            margin-left: auto;
            color: #999;
            font-size: 0.8rem;
        }

        .range-slider {
            margin-top: 15px;
        }

        .range-values {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 0.8rem;
            color: #666;
        }

        .reset-filters {
            width: 100%;
            background: var(--light-color);
            border: none;
            padding: 10px;
            border-radius: 5px;
            color: var(--secondary-color);
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .reset-filters:hover {
            background: #ddd;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

   :root {
    --primary-color: #3498db;
    --accent-color: #e74c3c;
    --secondary-color: #2c3e50;
    --light-color: #ecf0f1;
}

/* Sort Options */
.sort-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.sort-by {
    display: flex;
    align-items: center;
    gap: 10px;
}

.sort-by select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background: white;
    cursor: pointer;
}

.results-count {
    color: #666;
    font-size: 0.9rem;
}

/* Book Grid */
.book-grid {
    margin-top: 20px;
}

.book-grid.grid-view {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.book-grid.grid-view .book-card {
    width: calc(33.33% - 20px);
}

/* List View Fix */
.book-grid.list-view {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.book-grid.list-view .book-card {
    flex-direction: row;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
}

/* Book Card */
.book-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    border: 1px solid #ddd;
}

.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.book-cover {
    height: 220px;
    min-width: 150px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    border-radius: 10px;
}

.book-grid.list-view .book-cover {
    height: 150px;
    width: 120px;
    flex-shrink: 0;
}

.book-cover img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.3s;
}

.book-card:hover .book-cover img {
    transform: scale(1.05);
}

.book-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--accent-color);
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.7rem;
    font-weight: bold;
}

/* Book Details */
.book-details {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.book-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 5px;
    color: var(--secondary-color);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.book-author {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 8px;
}

.book-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 15px;
    font-size: 0.75rem;
}

.book-meta span {
    background: var(--light-color);
    padding: 3px 8px;
    border-radius: 3px;
    color: var(--secondary-color);
}

.book-actions {
    display: flex;
    justify-content: space-between;
    margin-top: auto;
    flex-wrap: wrap;
}

/* Buttons */
.btn-outline {
    background: transparent;
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
    padding: 6px 12px;
    border-radius: 5px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-outline:hover {
    background: var(--primary-color);
    color: white;
}

.wishlist-btn {
    background: transparent;
    border: none;
    color: #ccc;
    font-size: 1.1rem;
    cursor: pointer;
    transition: color 0.3s;
}

.wishlist-btn.active,
.wishlist-btn:hover {
    color: var(--accent-color);
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

.pagination-list {
    display: flex;
    list-style: none;
    gap: 5px;
}

.pagination-item {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.pagination-item a {
    text-decoration: none;
    color: var(--secondary-color);
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pagination-item:hover {
    background: #eee;
}

.pagination-item.active {
    background: var(--primary-color);
}

.pagination-item.active a {
    color: white;
}

.pagination-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Footer */
footer {
    background: #2c3e50;
    color: white;
    padding: 2rem 0 1rem;
    margin-top: auto;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-section h3 {
    font-size: 18px;
    margin-bottom: 1rem;
    color: #3498db;
    display: flex;
    align-items: center;
    gap: 8px;
}

.footer-section p,
.footer-section li {
    font-size: 14px;
    line-height: 1.8;
    opacity: 0.9;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 8px;
}

.footer-section ul li a {
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
    display: flex;
    align-items: flex-start;
    gap: 8px;
}

.footer-section ul li a:hover {
    color: #3498db;
}

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.social-links a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(52, 152, 219, 0.2);
    border-radius: 50%;
    color: white;
    font-size: 18px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.social-links a:hover {
    background: #3498db;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
}

/* Footer Bottom */
.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
}

.footer-left p {
    margin: 0;
    font-size: 14px;
    opacity: 0.8;
}

.footer-links {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.footer-links a {
    color: white;
    text-decoration: none;
    font-size: 14px;
    opacity: 0.8;
    transition: all 0.3s ease;
    padding: 4px 8px;
    border-radius: 4px;
}

.footer-links a:hover {
    opacity: 1;
    color: #3498db;
    background: rgba(52, 152, 219, 0.1);
}

/* Back to Top */
.back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3498db, #2c3e50);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    z-index: 1000;
}

.back-to-top:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.back-to-top.show {
    display: flex;
}

/* Responsive */
@media (max-width: 768px) {
    .book-grid.grid-view .book-card {
        width: calc(50% - 20px);
    }

    .footer-content {
        grid-template-columns: 1fr;
        text-align: left;
    }

    .footer-bottom {
        flex-direction: column;
        text-align: center;
    }

    .footer-links {
        justify-content: center;
    }

    .social-links {
        justify-content: center;
    }

    .footer-container {
        padding: 0 1rem;
    }
}

@media (max-width: 480px) {
    .book-grid.grid-view .book-card {
        width: 100%;
    }

    .footer-links {
        flex-direction: column;
        gap: 10px;
    }

    .back-to-top {
        bottom: 15px;
        right: 15px;
        width: 45px;
        height: 45px;
        font-size: 16px;
    }
}

/* Spinner */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

    </style>
</head>
<body>
  <!-- Ensure this HTML file is inside a PHP file like catalog.php -->
<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Home</a>
        <span class="separator">/</span>
        <span>Catalog</span>
    </div>

    <div class="catalog-tools">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" name="search" placeholder="Search by title, author, ISBN..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </div>

        <button class="filter-btn mobile-filter-btn">
            <i class="fas fa-filter"></i>
            Filters
        </button>

        <div class="view-options">
            <div class="view-option active" data-view="grid" title="Grid view">
                <i class="fas fa-th"></i>
            </div>
            <div class="view-option" data-view="list" title="List view">
                <i class="fas fa-list"></i>
            </div>
        </div>
    </div>

    <div class="catalog-content">
        <div class="sidebar">
            <h3>Refine Your Search</h3>

            <form id="filterForm" method="GET">
                <div class="filter-section">
                    <h4>Availability</h4>
                    <div class="filter-option">
                        <input type="checkbox" id="available" name="available" <?php echo isset($_GET['available']) ? 'checked' : ''; ?>>
                        <label for="available">Available Now</label>
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Format</h4>
                    <div class="filter-option">
                        <input type="checkbox" id="format-hardcover" name="format[]" value="hardcover" <?php echo (isset($_GET['format']) && in_array('hardcover', $_GET['format'])) ? 'checked' : ''; ?>>
                        <label for="format-hardcover">Hardcover</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="format-paperback" name="format[]" value="paperback" <?php echo (isset($_GET['format']) && in_array('paperback', $_GET['format'])) ? 'checked' : ''; ?>>
                        <label for="format-paperback">Paperback</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="format-ebook" name="format[]" value="ebook" <?php echo (isset($_GET['format']) && in_array('ebook', $_GET['format'])) ? 'checked' : ''; ?>>
                        <label for="format-ebook">E-book</label>
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Genre</h4>
                    <div class="filter-option">
                        <input type="checkbox" id="genre-fiction" name="genre[]" value="fiction" <?php echo (isset($_GET['genre']) && in_array('fiction', $_GET['genre'])) ? 'checked' : ''; ?>>
                        <label for="genre-fiction">Fiction</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="genre-nonfiction" name="genre[]" value="nonfiction" <?php echo (isset($_GET['genre']) && in_array('nonfiction', $_GET['genre'])) ? 'checked' : ''; ?>>
                        <label for="genre-nonfiction">Non-Fiction</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="genre-scifi" name="genre[]" value="science fiction" <?php echo (isset($_GET['genre']) && in_array('science fiction', $_GET['genre'])) ? 'checked' : ''; ?>>
                        <label for="genre-scifi">Science Fiction</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="genre-fantasy" name="genre[]" value="fantasy" <?php echo (isset($_GET['genre']) && in_array('fantasy', $_GET['genre'])) ? 'checked' : ''; ?>>
                        <label for="genre-fantasy">Fantasy</label>
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Publication Year</h4>
                    <div class="range-slider">
                        <input type="range" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo isset($_GET['year_min']) ? $_GET['year_min'] : '1900'; ?>" class="slider" id="yearMin" name="year_min">
                        <input type="range" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo isset($_GET['year_max']) ? $_GET['year_max'] : date('Y'); ?>" class="slider" id="yearMax" name="year_max">
                        <div class="range-values">
                            <span id="yearMinValue"><?php echo isset($_GET['year_min']) ? $_GET['year_min'] : '1900'; ?></span>
                            <span> - </span>
                            <span id="yearMaxValue"><?php echo isset($_GET['year_max']) ? $_GET['year_max'] : date('Y'); ?></span>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" id="hiddenSearchInput">
                <input type="hidden" name="sort" value="<?php echo isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'title-asc'; ?>" id="hiddenSortInput">

                <button type="button" class="reset-filters" id="resetFilters">Reset All Filters</button>
                <button type="submit" class="apply-filters">Apply Filters</button>
            </form>
        </div>

        <div class="main-content">
            <div class="sort-options">
                <div class="sort-by">
                    <span>Sort by:</span>
                    <select id="sortSelect">
                        <option value="title-asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'title-asc') ? 'selected' : ''; ?>>Title (A-Z)</option>
                        <option value="title-desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'title-desc') ? 'selected' : ''; ?>>Title (Z-A)</option>
                        <option value="author-asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'author-asc') ? 'selected' : ''; ?>>Author (A-Z)</option>
                        <option value="author-desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'author-desc') ? 'selected' : ''; ?>>Author (Z-A)</option>
                        <option value="year-desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'year-desc') ? 'selected' : ''; ?>>Publication Date (Newest)</option>
                        <option value="year-asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'year-asc') ? 'selected' : ''; ?>>Publication Date (Oldest)</option>
                        <option value="popular" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'popular') ? 'selected' : ''; ?>>Most Popular</option>
                    </select>
                </div>
                <div class="results-count">
                    Showing <?php echo isset($offset) ? ($offset + 1) : 1; ?>-<?php echo isset($offset, $limit, $totalBooks) ? min($offset + $limit, $totalBooks) : 10; ?> of <?php echo isset($totalBooks) ? $totalBooks : '?'; ?> results
                </div>
            </div>

            <div class="book-grid grid-view" id="bookGrid">
                <?php if (!empty($response['data']['books'])): ?>
                    <?php foreach ($response['data']['books'] as $book): ?>
                        <div class="book-card">
                            <div class="book-cover">
                                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <?php if (!$book['available']): ?>
                                    <div class="checked-out-badge">Checked Out</div>
                                <?php endif; ?>
                            </div>
                            <div class="book-details">
                                <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                                <p class="book-author"><?php echo htmlspecialchars($book['author']); ?></p>
                                <div class="book-meta">
                                    <span><?php echo htmlspecialchars($book['genre']); ?></span>
                                    <span><?php echo htmlspecialchars($book['publication_year']); ?></span>
                                </div>
                                <div class="book-actions">
                                    <button class="btn-outline">Details</button>
                                    <button class="wishlist-btn" title="Add to wishlist" data-book-id="<?php echo $book['id']; ?>">
                                        <i class="far fa-heart"></i>
                                        <span class="wishlist-count"><?php echo $book['wishlist_count']; ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-book-open"></i>
                        <p>No books found matching your criteria</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
<!-- 📜 JavaScript (Place at the end before </body>) -->
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const viewOptions = document.querySelectorAll('.view-option');
    const bookGrid = document.getElementById('bookGrid');
    
    viewOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            viewOptions.forEach(opt => opt.classList.remove('active'));
            // Add active class to clicked option
            this.classList.add('active');
            
            // Change view based on data-view attribute
            const viewType = this.getAttribute('data-view');
            bookGrid.classList.remove('grid-view', 'list-view');
            bookGrid.classList.add(viewType + '-view');
            
            // Store preference in localStorage
            localStorage.setItem('bookViewPreference', viewType);
        });
    });
    
    // Load saved view preference
    const savedView = localStorage.getItem('bookViewPreference') || 'grid';
    document.querySelector(`.view-option[data-view="${savedView}"]`).click();
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const hiddenSearchInput = document.getElementById('hiddenSearchInput');
    const filterForm = document.getElementById('filterForm');
    
    // Debounce function to limit how often search is triggered
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(context, args);
            }, wait);
        };
    }
    
    // Update hidden search input and submit form when typing
    searchInput.addEventListener('keyup', debounce(function() {
        hiddenSearchInput.value = this.value;
        filterForm.submit();
    }, 500));
    
    // Sort functionality
    const sortSelect = document.getElementById('sortSelect');
    const hiddenSortInput = document.getElementById('hiddenSortInput');
    
    sortSelect.addEventListener('change', function() {
        hiddenSortInput.value = this.value;
        filterForm.submit();
    });
    
    // Reset filters
    const resetFilters = document.getElementById('resetFilters');
    
    resetFilters.addEventListener('click', function() {
        // Uncheck all checkboxes
        document.querySelectorAll('#filterForm input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reset year range
        const yearMin = document.getElementById('yearMin');
        const yearMax = document.getElementById('yearMax');
        const yearMinValue = document.getElementById('yearMinValue');
        const yearMaxValue = document.getElementById('yearMaxValue');
        
        yearMin.value = 1900;
        yearMax.value = new Date().getFullYear();
        yearMinValue.textContent = 1900;
        yearMaxValue.textContent = new Date().getFullYear();
        
        // Clear search
        searchInput.value = '';
        hiddenSearchInput.value = '';
        
        // Reset sort
        sortSelect.value = 'title-asc';
        hiddenSortInput.value = 'title-asc';
        
        // Submit form
        filterForm.submit();
    });
    
    // Year range slider functionality
    const yearMin = document.getElementById('yearMin');
    const yearMax = document.getElementById('yearMax');
    const yearMinValue = document.getElementById('yearMinValue');
    const yearMaxValue = document.getElementById('yearMaxValue');
    
    yearMin.addEventListener('input', function() {
        yearMinValue.textContent = this.value;
        // Ensure min doesn't exceed max
        if (parseInt(this.value) > parseInt(yearMax.value)) {
            yearMax.value = this.value;
            yearMaxValue.textContent = this.value;
        }
    });
    
    yearMax.addEventListener('input', function() {
        yearMaxValue.textContent = this.value;
        // Ensure max doesn't go below min
        if (parseInt(this.value) < parseInt(yearMin.value)) {
            yearMin.value = this.value;
            yearMinValue.textContent = this.value;
        }
    });
    
    // Mobile filter button
    const mobileFilterBtn = document.querySelector('.mobile-filter-btn');
    const sidebar = document.querySelector('.sidebar');
    
    mobileFilterBtn.addEventListener('click', function() {
        sidebar.classList.toggle('mobile-visible');
    });
    
    // Wishlist functionality
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bookId = this.getAttribute('data-book-id');
            const heartIcon = this.querySelector('i');
            const countElement = this.querySelector('.wishlist-count');
            
            // Toggle between far (outline) and fas (solid) for Font Awesome
            if (heartIcon.classList.contains('far')) {
                heartIcon.classList.remove('far');
                heartIcon.classList.add('fas');
                
                // Increment count
                let currentCount = parseInt(countElement.textContent) || 0;
                countElement.textContent = currentCount + 1;
                
                // AJAX call to add to wishlist
                addToWishlist(bookId, true);
            } else {
                heartIcon.classList.remove('fas');
                heartIcon.classList.add('far');
                
                // Decrement count
                let currentCount = parseInt(countElement.textContent) || 0;
                countElement.textContent = Math.max(0, currentCount - 1);
                
                // AJAX call to remove from wishlist
                addToWishlist(bookId, false);
            }
        });
    });
    
    function addToWishlist(bookId, add) {
        // This would be an AJAX call to your backend
        fetch('wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                book_id: bookId,
                action: add ? 'add' : 'remove'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                // Revert UI changes if the operation failed
                console.error('Wishlist update failed:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Close mobile filters when clicking outside
    document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !mobileFilterBtn.contains(event.target)) {
            sidebar.classList.remove('mobile-visible');
        }
    });
});
</script>



<?php
// Helper function to build pagination URLs
function buildPaginationUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
?>
</body>
</html>
<?php include(__DIR__ . '/includes/footer.php');?>
