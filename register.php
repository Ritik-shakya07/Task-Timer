<?php
session_start();
require_once 'db.php';
// Include PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Determine current stage
$stage = $_SESSION['stage'] ?? 'register';
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle registration form submission
    if (isset($_POST['register'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        // Password rules check
        if (!preg_match('/^(?=.*[A-Z])(?=.*\W).{8,}$/', $password)) {
            $message = "Password must be at least 8 characters, include an uppercase letter and a special character.";
        } elseif ($password !== $confirm) {
            $message = "Passwords do not match.";
        } else {
            // Store registration data in session until OTP verified
            $_SESSION['reg_username'] = $username;
            $_SESSION['reg_email'] = $email;
            $_SESSION['reg_password'] = password_hash($password, PASSWORD_DEFAULT);

            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['reg_otp'] = $otp;
            $_SESSION['reg_otp_expiration'] = time() + 600; // 10 minutes

            // Send OTP via PHPMailer
            // Send OTP using PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    // Disable SSL certificate checks (allow self‐signed certs)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ],
    ];

    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ritikshakya7987@gmail.com';
    $mail->Password   = 'jqzaedsoemecsmbx';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('ritikshakya7987@gmail.com', 'Task Timer');
    $mail->addAddress($email, $username);
    $mail->Subject = 'Your Registration OTP';
    $mail->Body    = "Your OTP for registration is: " . $otp;

    $mail->send();

                // Move to OTP verification stage
                $_SESSION['stage'] = 'verify';
                $stage = 'verify';
                $message = "An OTP has been sent to your email. Please verify to complete registration.";
            } catch (Exception $e) {
                $message = "Error sending OTP: " . $mail->ErrorInfo;
            }
        }
    }

    

    // Handle OTP verification
    if (isset($_POST['verify'])) {
        $inputOtp = trim($_POST['otp']);
        $storedOtp = $_SESSION['reg_otp'] ?? '';
        $expires = $_SESSION['reg_otp_expiration'] ?? 0;

        if ($inputOtp == $storedOtp && time() <= $expires) {
            // Insert user into database
            $username = $conn->real_escape_string($_SESSION['reg_username']);
            $email    = $conn->real_escape_string($_SESSION['reg_email']);
            $password = $conn->real_escape_string($_SESSION['reg_password']);
            $conn->query("INSERT INTO users (username,email,password,is_verified) VALUES ('{$username}','{$email}','{$password}',1)");

            // Clear session registration vars
            unset($_SESSION['reg_username'], $_SESSION['reg_email'], $_SESSION['reg_password'],
                  $_SESSION['reg_otp'], $_SESSION['reg_otp_expiration'], $_SESSION['stage']);

            // Redirect to login
            header('Location: login.php');
            exit;
        } else {
            $message = "Invalid or expired OTP. Please try again.";
        }
    }
}


?>

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Task Timer</title>
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
        
        .otp-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        
        .otp-info {
            font-size: 14px;
            text-align: center;
            margin-bottom: 15px;
            color: var(--text-light);
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
        
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
        }

        .rules {
            font-size: 14px;
            color: var(--text-light);
            margin-top: -10px;
            margin-bottom: 20px;
        }
        #confirmMessage {
            font-size: 14px;
            margin-top: -10px;
            color: #f87171;
            text-align: right;
        }
        #confirmMessage.match {
            color: #4ade80;
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
        <?php if ($stage === 'register'): ?>
            <h2>Create Account</h2>
            <form method="POST" id="registrationForm">
                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                    <span class="input-icon"><i class="fas fa-user"></i></span>
                </div>
                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                </div>
                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <!-- Password Rules -->
                <div class="rules">
                    Password must be at least 8 characters long, include one uppercase letter and one special character.
                </div>
                <!-- Strength Meter -->
                <div class="strength-meter" id="strengthMeter"></div>
                <span id="strengthMessage"></span>
                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    <span class="password-toggle" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </span>
                    <span id="confirmMessage"></span>
                </div>
                <button type="submit" name="register" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>
            <div class="divider"><span>or</span></div>
            <p>Already have an account? <a href="login.php">Login here</a></p>

            
            <?php elseif ($stage === 'verify'): ?>
                <h2>Verify OTP</h2>
            <p class="otp-info">We've sent a 6-digit code to your email. Please enter it below to verify your account.</p>
            <<form method="POST">
                <div class="form-group">
                    <label for="otp" class="form-label">Verification Code</label>
                    <input type="text" id="otp" name="otp" class="form-control" required maxlength="6" placeholder="Enter 6-digit OTP">
                    <span class="input-icon"><i class="fas fa-key"></i></span>
                </div>
                <button type="submit" name="verify" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Verify OTP
                </button>
            </form>
            <p style="margin-top: 20px;">Didn't receive the code? <a href="#">Resend OTP</a></p>
        <?php endif; ?>
    </div>
    <script>
        function checkPasswordStrength() {
            var password = document.getElementById("password").value;
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
            var passwordInput = document.getElementById('password');
            if (passwordInput) {
                passwordInput.addEventListener('input', checkPasswordStrength);
            }
            
            // For mobile OTP input
            var otpInputs = document.querySelectorAll('.otp-input');
            otpInputs.forEach(function(input, index) {
                input.addEventListener('keyup', function(e) {
                    if (e.key >= 0 && e.key <= 9) {
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    } else if (e.key === 'Backspace') {
                        if (index > 0) {
                            otpInputs[index - 1].focus();
                        }
                    }
                });
            });
        });

        document.getElementById('password')?.addEventListener('input', checkPasswordStrength);
        /* Password Match Confirmation */
        const pwd = document.getElementById('password');
        const cpwd = document.getElementById('confirm_password');
        const confirmMsg = document.getElementById('confirmMessage');
        function checkMatch() {
            if (cpwd.value === '') {
                confirmMsg.textContent = '';
                return;
            }
            if (pwd.value === cpwd.value) {
                confirmMsg.textContent = 'Passwords match';
                confirmMsg.classList.add('match');
            } else {
                confirmMsg.textContent = 'Passwords do not match';
                confirmMsg.classList.remove('match');
            }
        }
        pwd.addEventListener('input', checkMatch);
        cpwd.addEventListener('input', checkMatch);
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
<!-- </html>
<h2>Register</h2>
<?php if($message) echo "<p class='error'>$message</p>"; ?>
        
<?php if ($stage == 'register'): ?>
  <form method="POST" action="register.php" oninput="checkPasswordStrength();">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" id="password" name="password" placeholder="Password" required>
      <span id="strengthMessage"></span>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <input type="submit" name="register" value="Register">
  </form>
<?php elseif ($stage == 'verify'): ?>
  <form method="POST" action="register.php">
      <input type="text" name="otp" placeholder="Enter OTP" required>
      <input type="submit" name="verify" value="Verify OTP">
  </form>
<?php endif; ?>
<p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html> -->
