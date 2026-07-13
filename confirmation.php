<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Confirmation</title>
    <link rel="stylesheet" href="confirmation.css" />
</head>

<body>
    <img src="1.png" alt="Gympro logo" class="logo">
    <div class="wrapper">
            <h1>Your subscription to the package has been successfully processed!</h1>
            <div class="btn">
                <!-- Add an onclick event to the button to redirect to Profile.php -->
                <br>
                <button class="button" onclick="redirectToProfile()">Profile</button>
                <!-- <button class="button">Profile</button> -->
                <br>
            </div>
            
    </div>

    <script>
        // JavaScript function to redirect to Profile.php
        function redirectToProfile() {
            window.location.href = 'Profile.php';
        }
    </script>

</body>
</html>