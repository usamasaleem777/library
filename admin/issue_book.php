<?php
include("../includes/header.php");
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];
    $issue_date = date("Y-m-d");

    mysqli_query($conn, "INSERT INTO issued_books (book_id, member_id, issue_date) VALUES ('$book_id', '$member_id', '$issue_date')");
}

$books = mysqli_query($conn, "SELECT * FROM books");
$members = mysqli_query($conn, "SELECT * FROM members");
?>

<h3>Issue Book</h3>
<form method="post">
    Book:
    <select name="book_id">
        <?php while ($b = mysqli_fetch_assoc($books)) {
            echo "<option value='{$b['id']}'>{$b['title']}</option>";
        } ?>
    </select><br>
    Member:
    <select name="member_id">
        <?php while ($m = mysqli_fetch_assoc($members)) {
            echo "<option value='{$m['id']}'>{$m['name']}</option>";
        } ?>
    </select><br>
    <button type="submit">Issue Book</button>
</form>
<?php include("../includes/footer.php"); ?>
