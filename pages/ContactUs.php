<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/ContactUs.css" type="text/css">
    <title>Contact Us</title>
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


    <!---------------------   Contact Start ---------------------->
    <section class="contact" id="contact">
        <div class="container">
            <div class="contactinfo">
                <div>
                    <h2>Contact Info</h2>
                    <hr>
                    <ul class="info">
                        <li>
                            <span><img src="../images/contact_images/mail.png" alt="location image"></span>
                            <span>GymPro@Gmail.com</span>
                        </li>
                        <li>
                            <span><img src="../images/contact_images/telephone.png" alt="location image"></span>
                            <span>011 1234 5678</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="social">
                        <li><a href="#"><img src="../images/contact_images/facebook.png" alt="facebook"></a></li>
                        <li><a href="#"><img src="../images/contact_images/twitter.png" alt="twitter"></a></li>
                        <li><a href="#"><img src="../images/contact_images/whatsapp.png" alt="whatsapp"></a></li>
                        <li><a href="#"><img src="../images/contact_images/youtube.png" alt="youtube"></a></li>
                        <li><a href="#"><img src="../images/contact_images/instagram.png" alt="instagram"></a></li>
                    </ul>

                </div>
            </div>
            <div class="contactForm">
                <h2>Send a Message</h2>
                <div class="formBox">
                    <div class="inputBox w50">
                        <input type="text" name="" required>
                        <span>First Name</span>
                    </div>
                    <div class="inputBox w50">
                        <input type="text" name="" required>
                        <span>Last Name</span>
                    </div>
                    <div class="inputBox w50">
                        <input type="text" name="" required>
                        <span>Email Address</span>
                    </div>
                    <div class="inputBox w50">
                        <input type="text" name="" required>
                        <span>Mobile Number</span>
                    </div>
                    <div class="inputBox w100">
                        <textarea name="" required></textarea>
                        <span>Write your message here...</span>
                    </div>
                    <div class="inputBox w100">
                        <input type="submit" value="Send">
                    </div>
                </div>
    </section>
    <!---------------------   Contact End ---------------------->
</body>
</html>