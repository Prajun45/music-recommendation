<?php
session_start();
include('db.php');

if (isset($_POST['submit_rating']) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $songId = $_POST['song_id'];
    $rating = $_POST['rating'];

    $sql = "SELECT * FROM user_ratings WHERE user_id = ? AND song_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $songId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sql = "UPDATE user_ratings SET rating = ? WHERE user_id = ? AND song_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $rating, $userId, $songId);
    } else {
        $sql = "INSERT INTO user_ratings (user_id, song_id, rating) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $songId, $rating);
    }

    $stmt->execute();
    header('Location: user_dashboard.php');
}
