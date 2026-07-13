<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Login Form</title>
    <link rel="stylesheet" href="LoginStyle.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <img src="1.png" alt="Gympro logo" class="logo">
    <div class="wrapper">
        <form action="handle_login.php" method="POST">
            <h1>Login</h1>
            <div class="input-box">
                <input type="text" name="email" placeholder="Email">
                <i class='bx bx-user-circle'></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" id="passwordInput" required>
                <i class='bx bx-lock' id="togglePassword"></i>
            </div>

            <div class="remember-box">
                <label class="container">
                    <input type="checkbox">
                    <svg viewBox="0 0 64 64" height="1.5em" width="1.5em">
                        <path
                            d="M 0 6 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                            pathLength="575.0541381835938" class="path"></path>
                    </svg>
                </label>
                <label for="remember">Remember me</label>
                <a href="Forget.php">Forgot Password?</a>
                <br>
            </div>

            <div class="btn">
                <br>
                <button class="button"></button>
                <br>
            </div>
            <div class="register-link">
                <p>Don't have an account? <a href="Register.php">Register</a></p>
            </div>
        </form>
    </div>
</body>

</html>
