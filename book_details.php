<?php
require_once('../config/db.php');
header('Content-Type: application/json');

$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($book_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid book ID']);
    exit;
}

// Get book details
$stmt = $conn->prepare("SELECT b.*, 
                       (SELECT COUNT(*) FROM wishlist w WHERE w.book_id = b.id) AS wishlist_count,
                       m.views, m.checkouts
                       FROM books b
                       LEFT JOIN book_metadata m ON b.id = m.book_id
                       WHERE b.id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Book not found']);
    exit;
}

$book = $result->fetch_assoc();

// Increment view count
if (!isset($_SESSION['viewed_books'][$book_id])) {
    $conn->query("INSERT INTO book_metadata (book_id, views) VALUES ($book_id, 1) 
                 ON DUPLICATE KEY UPDATE views = views + 1");
    $_SESSION['viewed_books'][$book_id] = true;
}

// Check if in wishlist (if user is logged in)
$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND book_id = ?");
    $check->bind_param("ii", $user_id, $book_id);
    $check->execute();
    $check->store_result();
    $in_wishlist = $check->num_rows > 0;
}

// Format response
$response = [
    'success' => true,
    'data' => [
        'book' => [
            'id' => $book['id'],
            'title' => $book['title'],
            'author' => $book['author'],
            'isbn' => $book['isbn'],
            'description' => $book['description'],
            'cover_image' => $book['cover_image'],
            'publication_year' => $book['publication_year'],
            'genre' => $book['genre'],
            'format' => $book['format'],
            'available' => (bool)$book['available'],
            'wishlist_count' => $book['wishlist_count'],
            'views' => $book['views'],
            'checkouts' => $book['checkouts']
        ],
        'in_wishlist' => $in_wishlist
    ]
];

echo json_encode($response);
?>