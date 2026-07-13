<?php
    // Start session to access session variables
    session_start();

    // Check if the user is logged in
    if(!isset($_SESSION['email'])) {
        // Redirect the user to the login page if not logged in
        header("Location: LoginPage.php");
        exit();
    }

    // Retrieve the logged-in email from the session
    $loggedInEmail = $_SESSION['email'];

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $card_id1 = filter_var($_POST['card_id1'], FILTER_VALIDATE_INT);
        $card_id2 = filter_var($_POST['card_id2'], FILTER_VALIDATE_INT);
        $card_id3 = filter_var($_POST['card_id3'], FILTER_VALIDATE_INT);
        $card_id4 = filter_var($_POST['card_id4'], FILTER_VALIDATE_INT);
        $cardHolderName = $_POST['card_holderName'];
        $expiry = $_POST['expiry'];
        $cvv = $_POST['cvv'];

        // Ensure that card_id1, card_id2, card_id3, and card_id4 are integers
        if ($card_id1 === false || $card_id2 === false || $card_id3 === false || $card_id4 === false) {
            // Handle invalid input (e.g., display an error message)
            echo "Invalid card ID. Please enter a valid integer value.";
            // You might want to redirect the user back to the form or handle the error in another way
            exit();
        }

        // Database connection
        $servername = "localhost"; // Change this if your MySQL server is running on a different host
        $username = "root"; // Default username for XAMPP MySQL
        $password = ""; // Default password for XAMPP MySQL
        $dbname = "gymster"; // Change this to the name of your database
        
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL to update data in table based on logged-in user's email
        $sql = "UPDATE members_data 
                SET card_id1='$card_id1', card_id2='$card_id2', card_id3='$card_id3', card_id4='$card_id4', 
                    card_holder_name='$cardHolderName', expiry='$expiry', cvv='$cvv' 
                WHERE email='$loggedInEmail'";

        if ($conn->query($sql) === TRUE) {
            // Redirect to confirmation.php after updating successfully
            header("Location: confirmation.php");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }

        $conn->close();
    }
?>
