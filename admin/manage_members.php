<?php
include("../includes/header.php");
include("../config/db.php");

$members = mysqli_query($conn, "SELECT * FROM members");
?>
<h3>Manage Members</h3>
<table border="1">
    <tr><th>ID</th><th>Name</th><th>Email</th></tr>
    <?php while ($row = mysqli_fetch_assoc($members)) { ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['email'] ?></td>
    </tr>
    <?php } ?>
</table>
<?php include("../includes/footer.php"); ?>
