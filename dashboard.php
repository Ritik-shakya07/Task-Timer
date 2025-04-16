<?php
session_start();
require_once 'db.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT username FROM users WHERE id='$user_id'");
$user = $result ? $result->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<style>
  body { font-family: Arial, sans-serif; margin: 20px; }
</style>
</head>
<body>
<h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
<p>This is your dashboard. You have successfully logged in.</p>
<p><a href="logout.php">Logout</a></p>
</body>
</html>
