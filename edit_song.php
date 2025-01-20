<?php
session_start();
include('db.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch song details
if (isset($_GET['id'])) {
    $songId = $_GET['id'];

    $sql = "SELECT * FROM songs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $songId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $song = $result->fetch_assoc();
    } else {
        echo "Song not found.";
        exit();
    }
}

// Update song details
if (isset($_POST['update_song'])) {
    $title = $_POST['title'];
    $artist = $_POST['artist'];
    $genre = $_POST['genre'];
    $album_cover_url = $_POST['album_cover_url'];

    $sql = "UPDATE songs SET title = ?, artist = ?, genre = ?, album_cover_url = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $title, $artist, $genre, $album_cover_url, $songId);

    if ($stmt->execute()) {
        echo "Song updated successfully!";
        header('Location: admin_dashboard.php'); // Redirect to admin dashboard
    } else {
        echo "Error updating song: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Song</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Edit Song</h1>
    <form action="edit_song.php?id=<?php echo $song['id']; ?>" method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($song['title']); ?>" required>

        <label for="artist">Artist:</label>
        <input type="text" name="artist" value="<?php echo htmlspecialchars($song['artist']); ?>" required>

        <label for="genre">Genre:</label>
        <input type="text" name="genre" value="<?php echo htmlspecialchars($song['genre']); ?>" required>

        <label for="album_cover_url">Album Cover URL:</label>
        <input type="file" name="album_cover_url" accept=".jpg, .jpeg, .png" value="<?php echo htmlspecialchars($song['album_cover_url']); ?>" required>

        <label for="music">Music:</label>
        <input type="file" name="music" accept=".mp3" value="<?php echo htmlspecialchars($song['song_url']); ?>" required>

        <button type="submit" name="update_song">Update Song</button>
    </form>
</body>

</html>