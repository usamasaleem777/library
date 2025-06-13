<?php
require_once('../config/db.php');
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($book_id <= 0 || !in_array($action, ['add', 'remove'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Check if book exists
$bookCheck = $conn->prepare("SELECT id FROM books WHERE id = ?");
$bookCheck->bind_param("i", $book_id);
$bookCheck->execute();
$bookCheck->store_result();

if ($bookCheck->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Book not found']);
    exit;
}

// Process wishlist action
if ($action === 'add') {
    // Check if already in wishlist
    $check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND book_id = ?");
    $check->bind_param("ii", $user_id, $book_id);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Book already in wishlist']);
        exit;
    }
    
    // Add to wishlist
    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, book_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $book_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Book added to wishlist']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add to wishlist']);
    }
} else {
    // Remove from wishlist
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param("ii", $user_id, $book_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Book removed from wishlist']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove from wishlist']);
    }
}
?>