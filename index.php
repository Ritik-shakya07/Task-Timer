<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Timer Dashboard</title>
  <!-- Font Awesome for any icon use if needed -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" 
        integrity="sha512-dNrXzljJ4ZxHk8bUSKnR0u9tC9Y5aVQv2Fhrw0OVX5C8MXWw1w6VRsJPlm5hS2YG/2o8l3K5Sxi1l1pO9z0fkw==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    /* Global Background & Fonts */
    body {
      background: linear-gradient(135deg, #e1f5fe, #ffffff);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      color: #333;
      min-height: 100vh;
      /* Reserve space for fixed header and footer */
      padding-top: 90px;
      padding-bottom: 120px;
    }
    /* Fixed Top Navigation Bar */
    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background: linear-gradient(90deg, #0288d1, #00acc1);
      color: #fff;
      padding: 20px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      z-index: 1000;
    }
    header .left {
      display: flex;
      align-items: center;
    }
    header img.logo {
      height: 50px;
      width: auto;
      margin-right: 15px;
    }
    header h1 {
      font-size: 24px;
      margin: 0;
    }
    /* Welcome Greeting – you can optionally use the username from the session */
    header .welcome {
      margin-left: 20px;
      font-size: 18px;
      color: #e0f7fa;
    }
    header nav a {
      color: #fff;
      text-decoration: none;
      margin-left: 20px;
      font-size: 18px;
    }
    /* Change the Logout button color if displayed on other pages */
    header nav a.logout {
      background: #ff9800; /* Orange tone */
      padding: 8px 14px;
      border-radius: 4px;
      transition: background 0.3s ease;
    }
    header nav a.logout:hover {
      background: #f57c00;
    }
    /* Main Content Container */
    .container {
      max-width: 800px;
      margin: 100px auto 100px auto;
      padding: 20px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      text-align: center;
    }
    .container h1 {
      color: #00796b;
      font-size: 36px;
      margin-bottom: 20px;
    }
    .container p {
      font-size: 18px;
      color: #333;
      margin: 20px 0;
      line-height: 1.5;
    }
    .btn {
      display: inline-block;
      padding: 10px 20px;
      margin: 10px;
      font-size: 18px;
      border-radius: 5px;
      text-decoration: none;
      transition: background 0.3s ease;
    }
    .btn-register {
      background: #26a69a;
      color: #fff;
    }
    .btn-register:hover {
      background: #1e88e5;
    }
    .btn-login {
      background: #00acc1;
      color: #fff;
    }
    .btn-login:hover {
      background: #00838f;
    }
    /* Fixed Footer */
    footer {
      background: #f1f1f1;
      color: #000;
      padding: 15px 30px;
      display: flex;
      justify-content: center;
      align-items: center;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      font-size: 14px;
      font-family: 'Georgia', serif;
      box-shadow: 0 -2px 6px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
  <!-- Fixed Navigation Bar -->
  <header>
    <div class="left">
      <!-- <img src="logo.png" alt="Logo" class="logo"> -->
      <h1>Task Timer project</h1>
      <!-- Optionally, if you have the username set in session, display a welcome greeting -->
      <?php if(isset($_SESSION['username'])): ?>
        <span class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
      <?php endif; ?>
    </div>
    <nav>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    </nav>
  </header>

  <!-- Main Content Area -->
  <div class="container">
    <h1>Welcome to Task Timer Dashboard</h1>
    <p>
      This project offers a robust task timer system designed to help you manage your tasks efficiently.
      Whether you're looking to track work, study sessions, or personal projects, our platform is built
      to optimize your productivity. To get started, please register or log in using the options above.
    </p>
    <!-- <p>
      <a href="register.php" class="btn btn-register">Register</a>
      <a href="login.php" class="btn btn-login">Login</a> -->
    </p>
  </div>

  <!-- Fixed Footer -->
  <footer>
    &copy; <?php echo date("Y"); ?> Task Timer Dashboard. All rights reserved.
  </footer>
</body>
</html>
