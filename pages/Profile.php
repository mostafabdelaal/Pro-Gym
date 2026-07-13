<?php
session_start(); // Start the session

// Check if the user is logged in, isset() returns null if the parameters are false
if (!isset($_SESSION['email'])) {
    // Redirect the user to the login page if not logged in
    header("Location: LoginPage.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gymster";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve user data for the logged-in user
$email = $_SESSION['email']; // Get the email from the session
$sql = "SELECT * FROM members_data WHERE email = '$email'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of the logged-in user
    $row = $result->fetch_assoc();
    $first_Name = $row['first_Name'];
    $last_name = $row['last_name'];
    $phone = $row['phone'];
    $birthdate = $row['birth_date'];
    $plan = isset($row['plan']) ? $row['plan'] : ''; // Set $plan to an empty string if it doesn't exist
    $card_id4 = isset($row['card_id4']) ? $row['card_id4'] : '';
} else {
    echo "0 results";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" type="text/css" href="../css/Profile.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>
    <img src="../images/1.png" alt="Gympro logo" class="logo">
    <input class="menu-icon" type="checkbox" id="menu-icon" name="menu-icon" />
    <label for="menu-icon"></label>
    <nav class="nav">
        <ul class="pt-5">
        <li><a href="MainStyleWithout.php">Home</a></li>
            <li><a href="Profile.php">Profile</a></li>
            <li><a href="Packages.php">Packages</a></li>
            <li><a href="AboutUs.php">About us</a></li>
            <li><a href="ContactUs.php">Contact us</a></li>
            <li><a href="Branches.php">Branches</a></li>
            <li><a href="Trainer.php">Trainers</a></li>
            <li><a href="MainPage.php">Log Out</a></li>
        </ul>
    </nav>

    <div class="card">
    <i class='bx bx-user'></i>
        <form>
        <h1>Hi, <?php echo $first_Name; ?> <?php echo $last_name; ?></h1>
        <label for="email">Email</label>
        <input type="text" id="email" name="text" value="<?php echo $email; ?>" class="input" readonly>
        <br><br>
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="text" value="<?php echo $phone; ?>" class="input" readonly>
        <br><br>
        <label for="birthdate">Birthdate</label>
        <input type="text" id="birthdate" name="birthdate" value="<?php echo $birthdate; ?>" class="input" readonly>
        <br><br>
        <label for="email">Plan</label>
        <input type="text" id="plan" name="text" value="<?php echo $plan; ?>" class="input" readonly>
        <br><br>
        <label for="email">Card ID</label>
        <input type="text" id="plan" placeholder=" X X X X X X X X X X X X" name="text" value="<?php echo $card_id4; ?>" class="input" readonly>
    </form>
    </div>      
</body>
</html>