<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Register Form</title>
    <link rel="stylesheet" href="../css/Reg.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>
    <img src="../images/1.png" alt="Gympro logo" class="logo">
    <div class="wrapper">
        <form action="../handlers/handle_registration.php" method="POST"> <!-- Modified action attribute -->
            <h1>Register</h1>
            <div class="first">
                <div class="input-box">
                    <input type="text" name="first_Name" placeholder="First Name" required> <!-- Added name attribute -->
                </div>
            
                <div class="input-box">
                    <input type="text" name="last_Name" placeholder="Last Name" required> <!-- Added name attribute -->
                </div>
            </div>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required> <!-- Added name attribute and changed type to email -->
               
            </div>

            <div class="input-box">
                <input type="text" name="phone" placeholder="Phone" required> <!-- Added name attribute -->
               
            </div>

            <div class="input-box">
                <input type="date" name="birth_date" placeholder="Birth Date" required> <!-- Added name attribute -->
               
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" id="passwordInput" required> <!-- Added name attribute -->
             
            </div>

      
            <div class="btn">
            <br>
                <button class="button"></button>
                <br>
            </div>
            </div>
            <!--<div class="register-link">
                <P>Don't have an account? <a href="#">Register</a></P>
            </div>-->
        </form>
    </div>
</body>
</html>
