<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include('db.php');

$sql = "SELECT * FROM songs";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav>
        <h1>Music Recommendation System</h1>
        <div>
            <a href="user_dashboard.php">Music</a>
            <a href="recommendation.php">Your Recommendations</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

        <h4>Music List</h4>
        <div id="songs-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="song-card">
                        <img src="<?php echo htmlspecialchars($row['album_cover_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?> Album Cover" loading="lazy">
                        <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p><strong>Artist:</strong> <?php echo htmlspecialchars($row['artist']); ?></p>
                        <p><strong>Genre:</strong> <?php echo htmlspecialchars($row['genre']); ?></p>

                        <!-- Add the audio player to play the song -->
                        <audio controls>
                            <source src="<?php echo htmlspecialchars($row['song_url']); ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>

                        <!-- Rating Form -->
                        <form method="POST" class="rating-form" action="rate_song.php">
                            <input type="hidden" name="song_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="radio" id="star1-<?php echo $row['id']; ?>" name="rating" value="1">
                            <label for="star1-<?php echo $row['id']; ?>" title="1 star">★</label>
                            <input type="radio" id="star2-<?php echo $row['id']; ?>" name="rating" value="2">
                            <label for="star2-<?php echo $row['id']; ?>" title="2 stars">★</label>
                            <input type="radio" id="star3-<?php echo $row['id']; ?>" name="rating" value="3">
                            <label for="star3-<?php echo $row['id']; ?>" title="3 stars">★</label>
                            <input type="radio" id="star4-<?php echo $row['id']; ?>" name="rating" value="4">
                            <label for="star4-<?php echo $row['id']; ?>" title="4 stars">★</label>
                            <input type="radio" id="star5-<?php echo $row['id']; ?>" name="rating" value="5">
                            <label for="star5-<?php echo $row['id']; ?>" title="5 stars">★</label>
                            <button type="submit" name="submit_rating">Rate</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No songs found.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> Music Recommendation System
    </footer>

</body>

</html>
<?php
$conn->close();
?>