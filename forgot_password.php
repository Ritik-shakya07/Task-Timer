<?php
session_start();
require_once 'db.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$stage = isset($_SESSION['fp_stage']) ? $_SESSION['fp_stage'] : 'request';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Request OTP for forgot password
    if (isset($_POST['request_otp'])) {
        $email = trim($_POST['email']);
        $emailEsc = $conn->real_escape_string($email);
        $result = $conn->query("SELECT * FROM users WHERE email='$emailEsc'");
        if ($result && $result->num_rows > 0) {
            $otp = rand(100000, 999999);
            $otp_expiration = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            $conn->query("UPDATE users SET otp='$otp', otp_expiration='$otp_expiration' WHERE email='$emailEsc'");
            
            // Send OTP using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'ritikshakya7987@gmail.com';
                $mail->Password   = 'jqzaedsoemecsmbx';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('no-reply@example.com', 'Your App Name');
                $mail->addAddress($email);
                $mail->Subject = 'Your Password Reset OTP';
                $mail->Body    = "Your OTP for password reset is: " . $otp;

                $mail->send();
                $_SESSION['fp_email'] = $email;
                $_SESSION['fp_stage'] = 'verify';
                $stage = 'verify';
                $message = "OTP sent to your email. Please enter it below.";
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                $message = "Error sending OTP. Please try again later.";
            }
        } else {
            $message = "Email not registered.";
        }
    }
    // Verify OTP for forgot password
    else if (isset($_POST['verify_otp'])) {
        $otp_input = trim($_POST['otp']);
        $email = $_SESSION['fp_email'];
        $emailEsc = $conn->real_escape_string($email);
        $result = $conn->query("SELECT otp, otp_expiration FROM users WHERE email='$emailEsc'");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['otp'] == $otp_input && strtotime($row['otp_expiration']) > time()) {
                $_SESSION['fp_stage'] = 'reset';
                $stage = 'reset';
                $message = "OTP verified. Please reset your password.";
            } else {
                $message = "Invalid or expired OTP.";
            }
        } else {
            $message = "User not found.";
        }
    }
    // Reset password after OTP verification
    else if (isset($_POST['reset_password'])) {
        $new_password = $_POST['new_password'];
        if (!preg_match('/^(?=.*[A-Z])(?=.*\W).{8,}$/', $new_password)) {
            $message = "Password must be at least 8 characters long, include one uppercase letter and one special character.";
        } elseif ($new_password !== $_POST['confirm_password']) {
            $message = "Passwords do not match.";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $email = $_SESSION['fp_email'];
            $emailEsc = $conn->real_escape_string($email);
            $conn->query("UPDATE users SET password='$hashed', otp=NULL, otp_expiration=NULL WHERE email='$emailEsc'");
            unset($_SESSION['fp_stage'], $_SESSION['fp_email']);
            $message = "Password reset successful. <a href='login.php'>Login here</a>.";
            $stage = 'done';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Task Timer</title>
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
        
        .success {
            padding: 12px;
            margin-bottom: 25px;
            background-color: rgba(74, 222, 128, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
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
        
        .strength-meter {
            height: 5px;
            margin-top: 10px;
            border-radius: 5px;
            background-color: #e5e7eb;
            position: relative;
            overflow: hidden;
        }
        
        .strength-meter::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            border-radius: 5px;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        
        .strength-meter.weak::before {
            width: 25%;
            background-color: var(--error-color);
        }
        
        .strength-meter.moderate::before {
            width: 50%;
            background-color: var(--warning-color);
        }
        
        .strength-meter.strong::before {
            width: 75%;
            background-color: var(--secondary-color);
        }
        
        .strength-meter.very-strong::before {
            width: 100%;
            background-color: var(--success-color);
        }
        
        #strengthMessage {
            font-size: 14px;
            margin-top: 8px;
            display: block;
            text-align: right;
        }
        
        .otp-info {
            font-size: 14px;
            text-align: center;
            margin-bottom: 20px;
            color: var(--text-light);
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e5e7eb;
            border: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .step-title {
            font-size: 12px;
            color: var(--text-light);
            position: absolute;
            top: 40px;
            white-space: nowrap;
        }
        
        .step-connector {
            flex: 1;
            height: 2px;
            background-color: #e5e7eb;
            margin: 0 10px;
            position: relative;
            top: 15px;
            z-index: 0;
        }
        
        .step.active .step-circle {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .step.active .step-title {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .step.completed .step-circle {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }
        
        .step.completed + .step-connector {
            background-color: var(--success-color);
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            
            .step-indicator {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 20px;
            }
            
            .step-connector {
                min-width: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <div class="logo">Task Timer</div>
        </div>
        
        <div class="step-indicator">
            <div class="step <?php echo ($stage == 'request' || $stage == 'verify' || $stage == 'reset' || $stage == 'done') ? 'active' : ''; ?> <?php echo ($stage == 'verify' || $stage == 'reset' || $stage == 'done') ? 'completed' : ''; ?>">
                <div class="step-circle">1</div>
                <div class="step-title">Email</div>
            </div>
            <div class="step-connector"></div>
            <div class="step <?php echo ($stage == 'verify' || $stage == 'reset' || $stage == 'done') ? 'active' : ''; ?> <?php echo ($stage == 'reset' || $stage == 'done') ? 'completed' : ''; ?>">
                <div class="step-circle">2</div>
                <div class="step-title">Verify OTP</div>
            </div>
            <div class="step-connector"></div>
            <div class="step <?php echo ($stage == 'reset' || $stage == 'done') ? 'active' : ''; ?> <?php echo ($stage == 'done') ? 'completed' : ''; ?>">
                <div class="step-circle">3</div>
                <div class="step-title">Reset Password</div>
            </div>
        </div>
        
        <h2>Recover Your Password</h2>
        
        <?php if($message): ?>
            <?php if($stage == 'done'): ?>
                <div class="success"><?php echo $message; ?></div>
            <?php else: ?>
                <div class="error"><?php echo $message; ?></div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($stage == 'request'): ?>
            <p class="otp-info">Enter your email address and we'll send you a verification code to reset your password.</p>
            <form method="POST" action="forgot_password.php">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                </div>
                
                <button type="submit" name="request_otp" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send OTP
                </button>
            </form>
            
        <?php elseif ($stage == 'verify'): ?>
            <p class="otp-info">We've sent a 6-digit code to your email. Please enter it below to continue.</p>
            <form method="POST" action="forgot_password.php">
                <div class="form-group">
                    <label for="otp" class="form-label">Verification Code</label>
                    <input type="text" id="otp" name="otp" class="form-control" required maxlength="6" placeholder="Enter 6-digit OTP">
                    <span class="input-icon"><i class="fas fa-key"></i></span>
                </div>
                
                <button type="submit" name="verify_otp" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Verify OTP
                </button>
            </form>
            
            <p style="margin-top: 20px;">Didn' receive the code? <a href="#">Resend OTP</a></p>
            
        <?php elseif ($stage == 'reset'): ?>
            <p class="otp-info">Create a new strong password for your account.</p>
            <form method="POST" action="forgot_password.php" id="resetForm">
                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                    <span class="password-toggle" onclick="togglePassword('new_password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="strength-meter" id="strengthMeter"></div>
                <span id="strengthMessage"></span>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    <span class="password-toggle" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <button type="submit" name="reset_password" class="btn btn-primary">
                    <i class="fas fa-lock"></i> Reset Password
                </button>
            </form>
            
        <?php elseif ($stage == 'done'): ?>
            <div class="success" style="text-align: center; padding: 20px;">
                <i class="fas fa-check-circle" style="font-size: 48px; color: var(--success-color); margin-bottom: 15px;"></i>
                <p style="font-size: 18px; font-weight: 600; color: var(--text-color);">Password Reset Successfully!</p>
                <p style="margin-bottom: 20px;">Your password has been updated. You can now login with your new password.</p>
                <a href="login.php" class="btn btn-primary" style="display: inline-block; text-decoration: none;">
                    <i class="fas fa-sign-in-alt"></i> Go to Login
                </a>
            </div>
        <?php endif; ?>
        
        <div class="divider">
            <span>or</span>
        </div>
        
        <p>Remember your password? <a href="login.php">Back to Login</a></p>
    </div>

    <script>
        function checkPasswordStrength() {
            var password = document.getElementById("new_password").value;
            var strengthMessage = document.getElementById("strengthMessage");
            var strengthMeter = document.getElementById("strengthMeter");
            
            // Remove all classes first
            strengthMeter.className = 'strength-meter';
            
            var strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/\W/.test(password)) strength++;
            
            if (password.length === 0) {
                strengthMessage.textContent = "";
            } else if (strength <= 1) {
                strengthMeter.classList.add('weak');
                strengthMessage.style.color = "var(--error-color)";
                strengthMessage.textContent = "Weak";
            } else if (strength == 2) {
                strengthMeter.classList.add('moderate');
                strengthMessage.style.color = "var(--warning-color)";
                strengthMessage.textContent = "Moderate";
            } else if (strength == 3) {
                strengthMeter.classList.add('strong');
                strengthMessage.style.color = "var(--secondary-color)";
                strengthMessage.textContent = "Strong";
            } else {
                strengthMeter.classList.add('very-strong');
                strengthMessage.style.color = "var(--success-color)";
                strengthMessage.textContent = "Very Strong";
            }
        }
        
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
        
        // Add event listeners when the document is loaded
        document.addEventListener('DOMContentLoaded', function() {
            var passwordInput = document.getElementById('new_password');
            if (passwordInput) {
                passwordInput.addEventListener('input', checkPasswordStrength);
            }
        });
    </script>
</body>
</html>