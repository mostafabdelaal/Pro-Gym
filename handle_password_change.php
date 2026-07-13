<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$database = "gymster"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$email = $_POST['email'];
$new_password = $_POST['password'];

// Prepare SQL statement to update the user's password in the database
$sql = "UPDATE members_data SET password='$new_password' WHERE email='$email'";

if ($conn->query($sql) === TRUE) {
    // Password changed successfully
    // Redirect to the login page
    header("Location: LoginPage.php");
    exit(); // Ensure script execution stops after redirection
} else {
    // Error occurred while changing password
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close database connection
$conn->close();
?>
