<?php
include("../includes/header.php");
include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];

    mysqli_query($conn, "INSERT INTO books (title, author, isbn) VALUES ('$title', '$author', '$isbn')");
}

$result = mysqli_query($conn, "SELECT * FROM books");
?>

<h3>Manage Books</h3>
<form method="post">
    Title: <input type="text" name="title"><br>
    Author: <input type="text" name="author"><br>
    ISBN: <input type="text" name="isbn"><br>
    <button type="submit">Add Book</button>
</form>

<h4>All Books</h4>
<table border="1">
    <tr><th>ID</th><th>Title</th><th>Author</th><th>ISBN</th></tr>
    <?php while ($book = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?= $book['id'] ?></td>
        <td><?= $book['title'] ?></td>
        <td><?= $book['author'] ?></td>
        <td><?= $book['isbn'] ?></td>
    </tr>
    <?php } ?>
</table>

<?php include("../includes/footer.php"); ?>
