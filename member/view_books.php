<?php include("../includes/header.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Collection | Ultra UI</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            
            --bg-primary: #0f0f23;
            --bg-secondary: #1a1a3e;
            --bg-tertiary: #242448;
            --bg-glass: rgba(255, 255, 255, 0.05);
            --bg-glass-hover: rgba(255, 255, 255, 0.1);
            
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;
            
            --border: rgba(255, 255, 255, 0.1);
            --border-hover: rgba(255, 255, 255, 0.2);
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            
            --radius-sm: 0.375rem;
            --radius: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
            
            --blur: blur(20px);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-attachment: fixed;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cdefs%3E%3Cfilter id='glow'%3E%3CfeGaussianBlur stdDeviation='3' result='coloredBlur'/%3E%3CfeMerge%3E%3CfeMergeNode in='coloredBlur'/%3E%3CfeMergeNode in='SourceGraphic'/%3E%3C/feMerge%3E%3C/filter%3E%3C/defs%3E%3Ccircle cx='20' cy='20' r='2' fill='rgba(255,255,255,0.1)' filter='url(%23glow)'%3E%3Canimate attributeName='cy' values='20;80;20' dur='10s' repeatCount='indefinite'/%3E%3C/circle%3E%3Ccircle cx='60' cy='80' r='1.5' fill='rgba(255,255,255,0.08)' filter='url(%23glow)'%3E%3Canimate attributeName='cy' values='80;20;80' dur='15s' repeatCount='indefinite'/%3E%3C/circle%3E%3Ccircle cx='90' cy='40' r='1' fill='rgba(255,255,255,0.06)' filter='url(%23glow)'%3E%3Canimate attributeName='cy' values='40;90;40' dur='12s' repeatCount='indefinite'/%3E%3C/circle%3E%3C/svg%3E") repeat;
            opacity: 0.6;
            z-index: -1;
            animation: float 20s ease-in-out infinite;
        }
        
        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        /* Header Styles */
        .header {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
        }
        
        .main-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            position: relative;
            animation: fadeInUp 1s ease-out;
        }
        
        .subtitle {
            color: var(--text-secondary);
            font-size: 1.25rem;
            max-width: 600px;
            margin: 0 auto 2rem;
            animation: fadeInUp 1s ease-out 0.2s both;
        }
        
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 2rem;
            animation: fadeInUp 1s ease-out 0.4s both;
        }
        
        .stat-item {
            text-align: center;
            padding: 1rem 2rem;
            background: var(--bg-glass);
            backdrop-filter: var(--blur);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            transition: all 0.3s ease;
        }
        
        .stat-item:hover {
            background: var(--bg-glass-hover);
            border-color: var(--border-hover);
            transform: translateY(-2px);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            display: block;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Search and Filter Section */
        .search-section {
            margin-bottom: 3rem;
            animation: fadeInUp 1s ease-out 0.6s both;
        }
        
        .search-container {
            background: var(--bg-glass);
            backdrop-filter: var(--blur);
            border: 1px solid var(--border);
            border-radius: var(--radius-2xl);
            padding: 2rem;
            box-shadow: var(--shadow-xl);
        }
        
        .search-row {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 1rem;
            align-items: end;
        }
        
        .search-field {
            position: relative;
        }
        
        .search-field label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        
        .search-input::placeholder {
            color: var(--text-muted);
        }
        
        .search-icon {
            position: absolute;
            left: 1rem;
            bottom: 1rem;
            color: var(--text-muted);
            pointer-events: none;
        }
        
        .filter-select {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            color: var(--text-primary);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 200px;
        }
        
        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        
        .view-toggle {
            display: flex;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-lg);
            padding: 0.25rem;
        }
        
        .view-btn {
            padding: 0.75rem 1rem;
            background: transparent;
            border: none;
            color: var(--text-muted);
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .view-btn.active {
            background: var(--primary);
            color: white;
            box-shadow: var(--shadow);
        }
        
        /* Books Grid */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .book-card {
            background: var(--bg-glass);
            backdrop-filter: var(--blur);
            border: 1px solid var(--border);
            border-radius: var(--radius-2xl);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            animation: fadeInUp 0.6s ease-out both;
        }
        
        .book-card:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: var(--border-hover);
            box-shadow: var(--shadow-2xl);
        }
        
        .book-cover {
            height: 280px;
            position: relative;
            overflow: hidden;
        }
        
        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .book-card:hover .book-cover img {
            transform: scale(1.1);
        }
        
        .book-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.8) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .book-card:hover .book-overlay {
            opacity: 1;
        }
        
        .quick-actions {
            display: flex;
            gap: 0.5rem;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        
        .book-card:hover .quick-actions {
            transform: translateY(0);
        }
        
        .quick-btn {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: var(--blur);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .quick-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            transform: scale(1.1);
        }
        
        .book-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, var(--accent) 0%, var(--warning) 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-xl);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: var(--shadow);
        }
        
        .book-details {
            padding: 1.5rem;
        }
        
        .book-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        
        .book-author {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .book-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        
        .meta-icon {
            color: var(--primary);
            font-size: 0.75rem;
        }
        
        .book-actions {
            display: flex;
            gap: 0.75rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            border-color: var(--border-hover);
        }
        
        /* Rating Stars */
        .book-rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-bottom: 1rem;
        }
        
        .star {
            color: var(--accent);
            font-size: 0.875rem;
        }
        
        .rating-text {
            margin-left: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        
        /* Loading States */
        .loading-card {
            background: var(--bg-glass);
            border: 1px solid var(--border);
            border-radius: var(--radius-2xl);
            padding: 1.5rem;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        .loading-cover {
            height: 280px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            margin-bottom: 1rem;
        }
        
        .loading-line {
            height: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--radius);
            margin-bottom: 0.5rem;
        }
        
        .loading-line.short {
            width: 60%;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 3rem;
        }
        
        .page-btn {
            width: 48px;
            height: 48px;
            background: var(--bg-glass);
            backdrop-filter: var(--blur);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .page-btn:hover,
        .page-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-2px);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            grid-column: 1 / -1;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }
        
        .empty-title {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .empty-text {
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .books-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .main-title {
                font-size: 2.5rem;
            }
            
            .stats-bar {
                flex-wrap: wrap;
                gap: 1rem;
            }
            
            .stat-item {
                padding: 0.75rem 1.5rem;
            }
            
            .search-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .books-grid {
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                gap: 1.5rem;
            }
            
            .book-cover {
                height: 240px;
            }
        }
        
        @media (max-width: 480px) {
            .books-grid {
                grid-template-columns: 1fr;
            }
            
            .book-card {
                max-width: 400px;
                margin: 0 auto;
            }
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <header class="header">
            <h1 class="main-title">Literary Collection</h1>
            <p class="subtitle">Discover extraordinary stories from our carefully curated digital library</p>
            
            <div class="stats-bar">
                <div class="stat-item">
                    <span class="stat-number">2,547</span>
                    <span class="stat-label">Total Books</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">43</span>
                    <span class="stat-label">Genres</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">892</span>
                    <span class="stat-label">Authors</span>
                </div>
            </div>
        </header>
        
        <!-- Search and Filter Section -->
        <section class="search-section">
            <div class="search-container">
                <div class="search-row">
                    <div class="search-field">
                        <label for="search">Search Books</label>
                        <div style="position: relative;">
                            <input type="text" id="search" class="search-input" placeholder="Search by title, author, or ISBN...">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>
                    
                    <div class="search-field">
                        <label for="genre">Filter by Genre</label>
                        <select id="genre" class="filter-select">
                            <option value="">All Genres</option>
                            <option value="fiction">Fiction</option>
                            <option value="non-fiction">Non-Fiction</option>
                            <option value="science">Science</option>
                            <option value="biography">Biography</option>
                            <option value="fantasy">Fantasy</option>
                            <option value="mystery">Mystery</option>
                            <option value="romance">Romance</option>
                            <option value="thriller">Thriller</option>
                        </select>
                    </div>
                    
                    <div class="view-toggle">
                        <button class="view-btn active" data-view="grid">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button class="view-btn" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Books Grid -->
        <main class="books-grid" id="booksContainer">
            <!-- Sample Book Cards -->
            <article class="book-card" style="animation-delay: 0.1s">
                <div class="book-cover">
                    <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&h=600&fit=crop" alt="The Great Adventure">
                    <div class="book-overlay">
                        <div class="quick-actions">
                            <button class="quick-btn" title="Quick View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="quick-btn" title="Add to Wishlist">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="quick-btn" title="Share">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                    <span class="book-badge">Featured</span>
                </div>
                <div class="book-details">
                    <h3 class="book-title">The Great Adventure</h3>
                    <p class="book-author">
                        <i class="fas fa-user-pen"></i>
                        Alexandra Thompson
                    </p>
                    <div class="book-rating">
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star-half-alt star"></i>
                        <span class="rating-text">4.5 (89 reviews)</span>
                    </div>
                    <div class="book-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar meta-icon"></i>
                            <span>2024</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-book-open meta-icon"></i>
                            <span>267 pages</span>
                        </div>
                    </div>
                    <div class="book-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-book-reader"></i>
                            Read Now
                        </button>
                        <button class="btn btn-secondary">
                            <i class="fas fa-bookmark"></i>
                        </button>
                    </div>
                </div>
            </article>
            
            <article class="book-card" style="animation-delay: 0.4s">
                <div class="book-cover">
                    <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?w=400&h=600&fit=crop" alt="Ocean's Whisper">
                    <div class="book-overlay">
                        <div class="quick-actions">
                            <button class="quick-btn" title="Quick View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="quick-btn" title="Add to Wishlist">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="quick-btn" title="Share">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="book-details">
                    <h3 class="book-title">Ocean's Whisper</h3>
                    <p class="book-author">
                        <i class="fas fa-user-pen"></i>
                        James Maritime
                    </p>
                    <div class="book-rating">
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="far fa-star star"></i>
                        <span class="rating-text">4.1 (312 reviews)</span>
                    </div>
                    <div class="book-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar meta-icon"></i>
                            <span>2023</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-book-open meta-icon"></i>
                            <span>398 pages</span>
                        </div>
                    </div>
                    <div class="book-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-book-reader"></i>
                            Read Now
                        </button>
                        <button class="btn btn-secondary">
                            <i class="fas fa-bookmark"></i>
                        </button>
                    </div>
                </div>
            </article>
            
            <article class="book-card" style="animation-delay: 0.5s">
                <div class="book-cover">
                    <img src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=400&h=600&fit=crop" alt="Stellar Journey">
                    <div class="book-overlay">
                        <div class="quick-actions">
                            <button class="quick-btn" title="Quick View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="quick-btn" title="Add to Wishlist">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="quick-btn" title="Share">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                    <span class="book-badge">Bestseller</span>
                </div>
                <div class="book-details">
                    <h3 class="book-title">Stellar Journey</h3>
                    <p class="book-author">
                        <i class="fas fa-user-pen"></i>
                        Dr. Sarah Nova
                    </p>
                    <div class="book-rating">
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <span class="rating-text">4.9 (567 reviews)</span>
                    </div>
                    <div class="book-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar meta-icon"></i>
                            <span>2024</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-book-open meta-icon"></i>
                            <span>445 pages</span>
                        </div>
                    </div>
                    <div class="book-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-book-reader"></i>
                            Read Now
                        </button>
                        <button class="btn btn-secondary">
                            <i class="fas fa-bookmark"></i>
                        </button>
                    </div>
                </div>
            </article>
            
            <article class="book-card" style="animation-delay: 0.6s">
                <div class="book-cover">
                    <img src="https://images.unsplash.com/photo-1589998059171-988d887df646?w=400&h=600&fit=crop" alt="Ancient Wisdom">
                    <div class="book-overlay">
                        <div class="quick-actions">
                            <button class="quick-btn" title="Quick View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="quick-btn" title="Add to Wishlist">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="quick-btn" title="Share">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="book-details">
                    <h3 class="book-title">Ancient Wisdom</h3>
                    <p class="book-author">
                        <i class="fas fa-user-pen"></i>
                        Professor Liu Wei
                    </p>
                    <div class="book-rating">
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star-half-alt star"></i>
                        <span class="rating-text">4.6 (203 reviews)</span>
                    </div>
                    <div class="book-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar meta-icon"></i>
                            <span>2023</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-book-open meta-icon"></i>
                            <span>512 pages</span>
                        </div>
                    </div>
                    <div class="book-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-book-reader"></i>
                            Read Now
                        </button>
                        <button class="btn btn-secondary">
                            <i class="fas fa-bookmark"></i>
                        </button>
                    </div>
                </div>
            </article>
        </main>
        
        <!-- Pagination -->
        <nav class="pagination">
            <a href="#" class="page-btn">
                <i class="fas fa-chevron-left"></i>
            </a>
            <a href="#" class="page-btn active">1</a>
            <a href="#" class="page-btn">2</a>
            <a href="#" class="page-btn">3</a>
            <span style="color: var(--text-muted); padding: 0 1rem;">...</span>
            <a href="#" class="page-btn">12</a>
            <a href="#" class="page-btn">
                <i class="fas fa-chevron-right"></i>
            </a>
        </nav>
    </div>
    
    <script>
        // Enhanced Search Functionality
        const searchInput = document.getElementById('search');
        const genreSelect = document.getElementById('genre');
        const booksContainer = document.getElementById('booksContainer');
        const bookCards = document.querySelectorAll('.book-card');
        
        // Debounce function for search
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Search functionality with animation
        const performSearch = debounce((searchTerm, genre) => {
            const filteredCards = Array.from(bookCards).filter(card => {
                const title = card.querySelector('.book-title').textContent.toLowerCase();
                const author = card.querySelector('.book-author').textContent.toLowerCase();
                
                const matchesSearch = !searchTerm || 
                    title.includes(searchTerm.toLowerCase()) || 
                    author.includes(searchTerm.toLowerCase());
                
                const matchesGenre = !genre || card.dataset.genre === genre;
                
                return matchesSearch && matchesGenre;
            });
            
            // Animate cards out
            bookCards.forEach((card, index) => {
                card.style.transition = 'all 0.3s ease';
                card.style.transform = 'translateY(20px)';
                card.style.opacity = '0';
                
                setTimeout(() => {
                    if (filteredCards.includes(card)) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.transform = 'translateY(0)';
                            card.style.opacity = '1';
                        }, 50);
                    } else {
                        card.style.display = 'none';
                    }
                }, index * 50);
            });
            
            // Show no results message if needed
            setTimeout(() => {
                if (filteredCards.length === 0 && !document.querySelector('.empty-state')) {
                    const emptyState = document.createElement('div');
                    emptyState.className = 'empty-state';
                    emptyState.innerHTML = `
                        <i class="fas fa-search empty-icon"></i>
                        <h3 class="empty-title">No Books Found</h3>
                        <p class="empty-text">Try adjusting your search terms or filters</p>
                        <button class="btn btn-primary" onclick="clearSearch()">
                            <i class="fas fa-refresh"></i>
                            Clear Search
                        </button>
                    `;
                    booksContainer.appendChild(emptyState);
                } else if (filteredCards.length > 0) {
                    const existingEmpty = document.querySelector('.empty-state');
                    if (existingEmpty) {
                        existingEmpty.remove();
                    }
                }
            }, 500);
        }, 300);
        
        // Clear search function
        window.clearSearch = function() {
            searchInput.value = '';
            genreSelect.value = '';
            performSearch('', '');
        };
        
        // Event listeners
        searchInput.addEventListener('input', (e) => {
            performSearch(e.target.value, genreSelect.value);
        });
        
        genreSelect.addEventListener('change', (e) => {
            performSearch(searchInput.value, e.target.value);
        });
        
        // View toggle functionality
        const viewButtons = document.querySelectorAll('.view-btn');
        viewButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                viewButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const view = btn.dataset.view;
                if (view === 'list') {
                    booksContainer.style.gridTemplateColumns = '1fr';
                    booksContainer.style.gap = '1rem';
                    bookCards.forEach(card => {
                        card.style.display = 'flex';
                        card.style.height = '200px';
                        const cover = card.querySelector('.book-cover');
                        const details = card.querySelector('.book-details');
                        cover.style.width = '150px';
                        cover.style.height = '100%';
                        details.style.flex = '1';
                        details.style.display = 'flex';
                        details.style.flexDirection = 'column';
                        details.style.justifyContent = 'space-between';
                    });
                } else {
                    booksContainer.style.gridTemplateColumns = 'repeat(auto-fill, minmax(320px, 1fr))';
                    booksContainer.style.gap = '2rem';
                    bookCards.forEach(card => {
                        card.style.display = 'block';
                        card.style.height = 'auto';
                        const cover = card.querySelector('.book-cover');
                        const details = card.querySelector('.book-details');
                        cover.style.width = '100%';
                        cover.style.height = '280px';
                        details.style.flex = 'none';
                        details.style.display = 'block';
                    });
                }
            });
        });
        
        // Enhanced favorite functionality
        document.querySelectorAll('.quick-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const icon = this.querySelector('i');
                
                if (icon.classList.contains('fa-heart')) {
                    // Toggle favorite
                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.style.background = 'var(--danger)';
                        this.style.borderColor = 'var(--danger)';
                        
                        // Add floating heart animation
                        const heart = document.createElement('i');
                        heart.className = 'fas fa-heart';
                        heart.style.cssText = `
                            position: absolute;
                            color: var(--danger);
                            font-size: 1.5rem;
                            pointer-events: none;
                            animation: floatHeart 1s ease-out forwards;
                        `;
                        
                        const style = document.createElement('style');
                        style.textContent = `
                            @keyframes floatHeart {
                                0% { transform: translateY(0) scale(1); opacity: 1; }
                                100% { transform: translateY(-30px) scale(1.5); opacity: 0; }
                            }
                        `;
                        document.head.appendChild(style);
                        
                        this.appendChild(heart);
                        setTimeout(() => heart.remove(), 1000);
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.style.background = 'rgba(255, 255, 255, 0.2)';
                        this.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                    }
                }
            });
        });
        
    
       
        
        // Add loading states for dynamic content
        function showLoadingCards(count = 6) {
            booksContainer.innerHTML = '';
            for (let i = 0; i < count; i++) {
                const loadingCard = document.createElement('div');
                loadingCard.className = 'loading-card';
                loadingCard.innerHTML = `
                    <div class="loading-cover"></div>
                    <div class="loading-line"></div>
                    <div class="loading-line short"></div>
                    <div class="loading-line"></div>
                `;
                booksContainer.appendChild(loadingCard);
            }
        }
        
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe all book cards for scroll animations
        bookCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `all 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });
        
        // Add CSS for fadeOut animation
        const fadeOutStyle = document.createElement('style');
        fadeOutStyle.textContent = `
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(20px); }
            }
        `;
        document.head.appendChild(fadeOutStyle);
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', () => {
            // Add stagger animation to initial load
            bookCards.forEach((card, index) => {
                setTimeout(() => {
                    observer.observe(card);
                }, index * 100);
            });
        });
    </script>
</body>
<?php include("../includes/footer.php");?>
