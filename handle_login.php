<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Default username for XAMPP MySQL
$password = ""; // Default password for XAMPP MySQL
$database = "gymster";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve email and password from form
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare SQL statement to check if email exists
$sql = "SELECT * FROM members_data WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Email exists, fetch user data
    $row = $result->fetch_assoc();
    
    // Check if password matches
    if ($password == $row['password']) {
        // Password matches, redirect to MainPage.php with welcome message
        session_start(); // Start the session
        $_SESSION['email'] = $email; // Store user's email in session variable
        header("Location: MainStyleWithout.php?welcome=1"); // Redirect to MainPage.php with welcome parameter
        exit();
    } else {
        // Password is incorrect, redirect back to login page with error message
        header("Location: LoginPage.php?error=incorrect_password"); // Redirect with error parameter
        exit();
    }
} else {
    // Email not found, redirect back to login page with error message
    header("Location: LoginPage.php?error=email_not_found"); // Redirect with error parameter
    exit();
}

// Close database connection
$conn->close();
?>
