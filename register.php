<?php
include('db.php');

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<nav>
    <h1>Music Recommendation System</h1>
    <div>
        <a href="index.php">Home</a>
    </div>
</nav>
<form method="POST">
    <link rel="stylesheet" href="style.css">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="register">Register</button>
    <p class="mt-4">Already register an account <a href="login.php" class="text-danger">Login
        </a></p>
</form>