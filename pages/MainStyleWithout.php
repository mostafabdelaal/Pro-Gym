<?php
// Legacy logged-in homepage — replaced by Dashboard.php.
// Forward all traffic to the new dashboard.
header("Location: Dashboard.php");
exit();
?>
