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

// Retrieve and sanitize form data
$first_name = htmlspecialchars($_POST['first_Name']);
$last_name = htmlspecialchars($_POST['last_Name']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$phone = $_POST['phone'];
$birth_date = $_POST['birth_date'];
$password = $_POST['password'];

// Prepare SQL statement to insert data into members_data table
$sql = "INSERT INTO members_data (first_name, last_name, email, phone, birth_date, password)
        VALUES ('$first_name', '$last_name', '$email', '$phone', '$birth_date', '$password')";

if ($conn->query($sql) === TRUE) {
    // Registration successful
    echo "Registration complete. Redirecting to login page...";
    // Redirect to LoginPage.php after 2 seconds
    header("refresh:2;url=LoginPage.php");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close database connection
$conn->close();
?>
