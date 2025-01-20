<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
include('db.php');

// Handle song deletion, editing, and adding here.

$sql = "SELECT * FROM songs";
$result = $conn->query($sql);
?>
<nav>
    <h1>Music Recommendation System</h1>
    <div>
        <a href="add_song.php">Add Song</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>
<link rel="stylesheet" href="style.css">
<table>
    <tr>
        <th>Title</th>
        <th>Artist</th>
        <th>Genre</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['artist']); ?></td>
            <td><?php echo htmlspecialchars($row['genre']); ?></td>
            <td>
                <a href="edit_song.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a href="delete_song.php?id=<?php echo $row['id']; ?>">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #f8f8f8;
    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    button,
    a {
        margin-right: 10px;
    }
</style>