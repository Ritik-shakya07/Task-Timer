<?php
session_start();
require_once 'db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['is_verified'] != 1) {
            $message = "Account not verified. Please complete registration.";
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            // Redirect to greeting page after login
            header("Location: task_timer.php");
            exit();
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        $message = "Invalid email or password.";
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Your App Name</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --secondary-color: #4cc9f0;
            --success-color: #4ade80;
            --warning-color: #fbbf24;
            --error-color: #f87171;
            --text-color: #374151;
            --text-light: #6b7280;
            --bg-color: #f9fafb;
            --card-bg: #ffffff;
            --border-radius: 12px;
            --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 450px;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 40px;
            box-shadow: var(--box-shadow);
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            letter-spacing: 0.5px;
        }
        
        h2 {
            color: var(--text-color);
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 24px;
        }
        
        p {
            margin: 15px 0;
            color: var(--text-light);
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #f9fafb;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        
        .form-label {
            position: absolute;
            top: -10px;
            left: 15px;
            padding: 0 5px;
            background-color: var(--card-bg);
            color: var(--text-light);
            font-size: 14px;
        }
        
        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 15px;
            color: var(--text-light);
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .error {
            padding: 12px;
            margin-bottom: 25px;
            background-color: rgba(248, 113, 113, 0.1);
            border-left: 4px solid var(--error-color);
            color: var(--error-color);
            border-radius: 4px;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
        }
        
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #e5e7eb;
        }
        
        .divider span {
            padding: 0 10px;
            color: var(--text-light);
            font-size: 14px;
        }
        
        a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-light);
        }
        
        .forgot-password {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .social-login {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        
        .social-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background-color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .social-btn:hover {
            background-color: #f3f4f6;
        }
        
        .social-icon {
            font-size: 18px;
        }
        
        .google-icon {
            color: #ea4335;
        }
        
        .facebook-icon {
            color: #1877f2;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <div class="logo">Task Timer</div>
        </div>
        
        <?php if($message): ?>
            <div class="error"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <h2>Welcome back</h2>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
                <span class="input-icon"><i class="fas fa-envelope"></i></span>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <span class="password-toggle" onclick="togglePassword('password')">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            
            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
<!--         
        <div class="divider">
            <span>or continue with</span>
        </div>
        
        <div class="social-login">
            <button type="button" class="social-btn">
                <i class="fab fa-google social-icon google-icon"></i>
            </button>
            <button type="button" class="social-btn">
                <i class="fab fa-facebook-f social-icon facebook-icon"></i>
            </button>
        </div> -->
        
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
        function togglePassword(id) {
            var input = document.getElementById(id);
            var icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>