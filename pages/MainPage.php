<?php
session_start(); // Start the session
if(isset($_GET['welcome']) && $_GET['welcome'] == 1 && isset($_SESSION['email'])) {
    echo "Welcome ".$_SESSION['email']."!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Gym - Best Gym Center</title>
    <link rel="stylesheet" type="text/css" href="../css/MainStyle.css">
</head>
<body>
<img src="../images/1.png" alt="Gympro logo" class="logo">

    <div class="main-container">
        <h1 class="main-heading">Build Your Body Strong With Pro Gym</h1>
        <div class="button-container">
            <a href="Register.php" class="register-button">Register</a>
            <a href="LoginPage.php" class="login-button">Login</a>
        </div>
    </div>

</body>
</html>
