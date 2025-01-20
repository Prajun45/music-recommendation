<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
include('db.php');

if (isset($_POST['add_song'])) {
    // Get form data
    $title = $_POST['title'];
    $artist = $_POST['artist'];
    $genre = $_POST['genre'];

    // Handle album cover upload
    $albumCover = $_FILES['album_cover_url'];
    $coverFileName = basename($albumCover['name']);
    $coverFileTmp = $albumCover['tmp_name'];
    $coverFileSize = $albumCover['size'];
    $coverFileExt = strtolower(pathinfo($coverFileName, PATHINFO_EXTENSION));

    // Handle song file upload
    $songFile = $_FILES['music'];
    $songFileName = basename($songFile['name']);
    $songFileTmp = $songFile['tmp_name'];
    $songFileSize = $songFile['size'];
    $songFileExt = strtolower(pathinfo($songFileName, PATHINFO_EXTENSION));

    // Allowed file types
    $allowedImageTypes = ['jpg', 'jpeg', 'png'];
    $allowedAudioTypes = ['mp3'];

    // Define upload directories
    $coverUploadDir = 'uploads/images/';
    $songUploadDir = 'uploads/music/';

    // File size limits (in bytes)
    $maxCoverSize = 2 * 1024 * 1024;  // 2 MB for cover image
    $maxSongSize = 10 * 1024 * 1024;  // 10 MB for song file

    // Create unique file names to prevent overwriting
    $newCoverName = uniqid('', true) . '.' . $coverFileExt;
    $newSongName = uniqid('', true) . '.' . $songFileExt;

    // Validate file types and sizes
    if (in_array($coverFileExt, $allowedImageTypes) && in_array($songFileExt, $allowedAudioTypes)) {
        if ($coverFileSize <= $maxCoverSize && $songFileSize <= $maxSongSize) {
            if (
                move_uploaded_file($coverFileTmp, $coverUploadDir . $newCoverName) &&
                move_uploaded_file($songFileTmp, $songUploadDir . $newSongName)
            ) {
                // Insert song details into the database
                $albumCoverUrl = $coverUploadDir . $newCoverName;
                $songUrl = $songUploadDir . $newSongName;

                $sql = "INSERT INTO songs (title, artist, genre, album_cover_url, song_url) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssss", $title, $artist, $genre, $albumCoverUrl, $songUrl);

                if ($stmt->execute()) {
                    echo "Song added successfully!";
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error uploading files.";
            }
        } else {
            echo "File sizes exceed the allowed limit. Cover image max: 2MB, Song file max: 10MB.";
        }
    } else {
        echo "Invalid file types. Only JPG, JPEG, PNG images and MP3 audio are allowed.";
    }
}
?>

<!-- HTML Form for adding a song -->
<nav>
    <h1>Music Recommendation System</h1>
    <div>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>
<form method="POST" enctype="multipart/form-data">
    <link rel="stylesheet" href="style.css">
    <input type="text" name="title" placeholder="Song Title" required>
    <input type="text" name="artist" placeholder="Artist" required>
    <input type="text" name="genre" placeholder="Genre" required>

    <!-- Upload Album Cover -->
    <label for="cover_image">Upload Cover Image (JPEG or PNG, max 2MB):</label>
    <input type="file" name="album_cover_url" accept=".jpg, .jpeg, .png" required>

    <!-- Upload Song File -->
    <label for="music">Upload Song (MP3, max 10MB):</label>
    <input type="file" id="music" name="music" accept=".mp3" required>

    <button type="submit" name="add_song">Add Song</button>
</form>