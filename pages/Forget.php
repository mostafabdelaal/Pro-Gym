<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Password Change Form</title>
    <link rel="stylesheet" href="../css/Forget.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <img src="../images/1.png" alt="Gympro logo" class="logo">
    <div class="wrapper">
        <form action="../handlers/handle_password_change.php" method="POST"> <!-- Specify action and method -->
            <h1>Password Change</h1>
            <br><br><br>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required> <!-- Changed type to email and added name attribute -->
                <i class='bx bx-user-circle'></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="New Password" id="passwordInput" required> <!-- Changed name attribute -->
                <i class='bx bx-lock' id="togglePassword"></i>
            </div>
            <div class="btn">
                <br>
                <button class="button"></button>
                <br>
            </div>
        </form>
    </div>
</body>

</html>
