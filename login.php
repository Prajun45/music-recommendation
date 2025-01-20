<?php
session_start();
include('db.php');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: user_dashboard.php');
        }
    } else {
        echo "Invalid username or password.";
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
    <button type="submit" name="login">Login</button>
    <p class="mt-4">Don't have an account? <a href="register.php" class="text-danger">Register
        </a></p>
</form>