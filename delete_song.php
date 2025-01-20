<?php
session_start();
include('db.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Delete the song if song ID is provided
if (isset($_GET['id'])) {
    $songId = $_GET['id'];

    $sql = "DELETE FROM songs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $songId);

    if ($stmt->execute()) {
        echo "Song deleted successfully!";
        header('Location: admin_dashboard.php'); // Redirect to admin dashboard
    } else {
        echo "Error deleting song: " . $conn->error;
    }
} else {
    echo "No song ID provided.";
}
