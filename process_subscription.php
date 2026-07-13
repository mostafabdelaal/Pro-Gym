<?php
// Start session
session_start();

// Check if email is set in session
if(isset($_SESSION['email'])) {
    // Retrieve email from session
    $email = $_SESSION['email'];
    
    // Retrieve plan from form
    $plan = $_POST['plan']; // This will be either "BEGINNER" or "INTERMEDIATE" or "EXPERT" or "ADVANCED" or "ELITE"
    
    // Database connection settings
    $servername = "localhost";
    $username = "root"; // Change this if you have a different username
    $password = ""; // Change this if you have set a password for MySQL
    $dbname = "gymster"; // Change this if your database name is different
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Prepare SQL statement to update plan for the given email
    $stmt = $conn->prepare("UPDATE members_data SET plan = ? WHERE email = ?");
    
    $stmt->bind_param("ss", $plan, $email);
    
    // Execute SQL statement to update plan
    if ($stmt->execute()) {
        // Close statement
        $stmt->close();
        
        // Close connection
        $conn->close();
        
        // Redirect to payment page
        header("Location: Payment.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    // Redirect to login page or handle unauthorized access
    header("Location: login.php");
    exit();
}
?>
