<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" type="text/css" href="style.css">

</head>

<body>
    <nav>
        <h1>Music Recommendation System</h1>
        <div>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </nav>
    <p>Login to view personalized music recommendations or, if you are an admin, to add new music.</p>

    <img src="uploads/images/img2.png" class="floating-image" alt="Floating Music Note 2">
    <img src="uploads/images/img3.png" class="floating-image" alt="Floating Music Note 3">

</body>
<style>
    floating-image {
        position: absolute;
        width: 150px;
        height: 150px;
        opacity: 0.9;
        animation: float 5s ease-in-out infinite;
    }

    /* Different animation timings for variety */

    .floating-image:nth-child(2) {
        top: 30%;
        right: 5%;
        animation-duration: 7s;
    }

    .floating-image:nth-child(3) {
        bottom: 10%;
        left: 20%;
        animation-duration: 8s;
    }


    /* Keyframes for smooth floating effect */
    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0px);
        }
    }
</style>


</html>