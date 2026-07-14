<?php
// Branch locations — from the branch repository (was hardcoded HTML).
require_once __DIR__ . '/../includes/auth.php';

$branches = app('branches')->allActive();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Branches</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="stylesheet" type="text/css" href="../css/Branches.css" />
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
    <h1>Branches</h1>
    <div class="wrapper">
        <?php if (empty($branches)): ?>
            <p style="color:#666;">No branches available yet.</p>
        <?php else: ?>
            <?php foreach ($branches as $b): ?>
                <?php $img = $b['image_path'] ?? ''; ?>
                <?php if ($img !== ''): ?>
                    <a href="../<?php echo htmlspecialchars($img); ?>" target="_blank" class="card">
                        <div class="card2">
                            <h2><br><br><?php echo htmlspecialchars($b['name']); ?> <br> Branch</h2>
                        </div>
                    </a>
                <?php else: ?>
                    <div class="card">
                        <div class="card2">
                            <h2><br><br><?php echo htmlspecialchars($b['name']); ?> <br> Branch</h2>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
