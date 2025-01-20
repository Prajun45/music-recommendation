<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    echo "<p>You need to log in to view recommendations.</p>";
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user ratings
function fetchUserRatings($conn, $userId)
{
    $stmt = $conn->prepare("SELECT song_id, rating FROM user_ratings WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userRatings = [];
    while ($row = $result->fetch_assoc()) {
        $userRatings[$row['song_id']] = $row['rating'];
    }
    return $userRatings;
}

// Fetch similar user ratings
function fetchSimilarUserRatings($conn, $userId)
{
    $stmt = $conn->prepare("
        SELECT ur2.user_id, ur2.song_id, ur2.rating
        FROM user_ratings ur1
        JOIN user_ratings ur2 ON ur1.song_id = ur2.song_id
        WHERE ur1.user_id = ? AND ur2.user_id != ?
    ");
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $similarUsersRatings = [];
    while ($row = $result->fetch_assoc()) {
        $similarUsersRatings[$row['user_id']][$row['song_id']] = $row['rating'];
    }
    return $similarUsersRatings;
}

// Calculate similarity
function calculateSimilarity($userRatings, $otherUserRatings)
{
    $sumUser = $sumOtherUser = $sumUserSq = $sumOtherUserSq = $sumProduct = $n = 0;

    foreach ($userRatings as $songId => $rating) {
        if (isset($otherUserRatings[$songId])) {
            $n++;
            $otherRating = $otherUserRatings[$songId];
            $sumUser += $rating;
            $sumOtherUser += $otherRating;
            $sumUserSq += pow($rating, 2);
            $sumOtherUserSq += pow($otherRating, 2);
            $sumProduct += $rating * $otherRating;
        }
    }

    if ($n === 0) return 0;
    $numerator = $sumProduct - (($sumUser * $sumOtherUser) / $n);
    $denominator = sqrt(($sumUserSq - (pow($sumUser, 2) / $n)) * ($sumOtherUserSq - (pow($sumOtherUser, 2) / $n)));
    return ($denominator == 0) ? 0 : $numerator / $denominator;
}

// Calculate recommendations
function getRecommendations($userRatings, $similarUsersRatings)
{
    $recommendations = [];
    foreach ($similarUsersRatings as $otherUserId => $otherUserRatings) {
        $similarity = calculateSimilarity($userRatings, $otherUserRatings);
        if ($similarity > 0) {
            foreach ($otherUserRatings as $songId => $rating) {
                if (!isset($userRatings[$songId])) {
                    $recommendations[$songId] = ($recommendations[$songId] ?? 0) + $rating * $similarity;
                }
            }
        }
    }
    arsort($recommendations);
    return $recommendations;
}

// Fetch fallback recommendations (popular items)
function fetchPopularSongs($conn, $limit = 5)
{
    $stmt = $conn->prepare("SELECT id, title, artist, genre, album_cover_url FROM songs ORDER BY id DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $songs = [];
    while ($row = $result->fetch_assoc()) {
        $songs[$row['id']] = $row;
    }
    return $songs;
}


// Fetch song details
function fetchSongDetails($conn, $songIds)
{
    if (empty($songIds)) return [];
    $placeholders = implode(',', array_fill(0, count($songIds), '?'));
    $stmt = $conn->prepare("SELECT id, title, artist, genre, album_cover_url FROM songs WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($songIds)), ...$songIds);
    $stmt->execute();
    $result = $stmt->get_result();
    $songs = [];
    while ($row = $result->fetch_assoc()) {
        $songs[$row['id']] = $row;
    }
    return $songs;
}

// Main logic
$userRatings = fetchUserRatings($conn, $userId);
$similarUsersRatings = fetchSimilarUserRatings($conn, $userId);
$recommendations = getRecommendations($userRatings, $similarUsersRatings);

// Display recommendations or fallbacks
function displayRecommendations($conn, $recommendations)
{
    if (!empty($recommendations)) {
        $songIds = array_keys($recommendations);
        $songs = fetchSongDetails($conn, $songIds);
        echo "<h2>Recommended Songs:</h2>";
        foreach ($recommendations as $songId => $score) {
            if (isset($songs[$songId])) {
                $song = $songs[$songId];
                echo "<div class='recommended-song'>";
                echo "<img src='" . htmlspecialchars($song['album_cover_url']) . "' alt='Album Cover' />";
                echo "<h3>" . htmlspecialchars($song['title']) . "</h3>";
                echo "<p><strong>Artist:</strong> " . htmlspecialchars($song['artist']) . "</p>";
                echo "<p><strong>Genre:</strong> " . htmlspecialchars($song['genre']) . "</p>";
                echo "<p><strong>Recommendation Score:</strong> " . number_format($score, 2) . "</p>";
                echo "</div>";
            }
        }
    } else {
        echo "<p>No personalized recommendations available. Here are some popular songs:</p>";
        $popularSongs = fetchPopularSongs($conn);
        foreach ($popularSongs as $song) {
            echo "<div class='recommended-song'>";
            echo "<img src='" . htmlspecialchars($song['album_cover_url']) . "' alt='Album Cover' />";
            echo "<h3>" . htmlspecialchars($song['title']) . "</h3>";
            echo "<p><strong>Artist:</strong> " . htmlspecialchars($song['artist']) . "</p>";
            echo "<p><strong>Genre:</strong> " . htmlspecialchars($song['genre']) . "</p>";
            echo "</div>";
        }
    }
}

displayRecommendations($conn, $recommendations);

$conn->close();
