<?php
session_start();
require_once 'db_connect.php';

$error = '';
$signup_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signin'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, full_name, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!isset($_POST['terms'])) {
        $error = "You must agree to the Terms & Conditions.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, phone, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $username, $email, $phone, $hashed_password]);

            // Create default accounts
            $user_id = $conn->lastInsertId();
            $checking_account = sprintf("%04d%04d%04d%04d", rand(1000, 9999), rand(1000, 9999), rand(1000, 9999), rand(1000, 9999));
            $savings_account = sprintf("%04d%04d%04d%04d", rand(1000, 9999), rand(1000, 9999), rand(1000, 9999), rand(1000, 9999));
            $conn->prepare("INSERT INTO accounts (user_id, account_type, account_number, balance) VALUES (?, 'Checking', ?, 0.00)")->execute([$user_id, $checking_account]);
            $conn->prepare("INSERT INTO accounts (user_id, account_type, account_number, balance) VALUES (?, 'Savings', ?, 0.00)")->execute([$user_id, $savings_account]);

            $signup_success = "Account created successfully! Please sign in.";
        } catch (PDOException $e) {
            $error = "Error: " . ($e->getCode() == 23000 ? "Email or username already exists." : $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BANKING SYSTEM</title>
    <link rel="stylesheet" href="stylelogin.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <header class="main-header">
                <span>🏦 BANKING SYSTEM</span>
                <p>Your trusted partner for secure digital banking</p>
                <ul>
                    <li>Secure transactions</li>
                    <li>24/7 customer support</li>
                    <li>Mobile banking</li>
                    <li>Instant transfers</li>
                </ul>
            </header>
            <section class="page-section active">
                <h1>Welcome</h1>
                <p>Please sign in to your account or create a new one</p>
                <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
                <?php if ($signup_success) echo "<p style='color: green;'>$signup_success</p>"; ?>
                <div class="actions">
                    <button class="btn" onclick="document.getElementById('signin-form').style.display='block';document.getElementById('signup-form').style.display='none';">Sign In</button>
                    <button class="btn" onclick="document.getElementById('signin-form').style.display='none';document.getElementById('signup-form').style.display='block';">Sign Up</button>
                </div>
                <form id="signin-form" method="POST" style="display: block;">
                    <div class="form-group">
                        <label for="email">Email or Username</label>
                        <input type="text" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="remember"> Remember me</label>
                        <a href="#">Forgot password?</a>
                    </div>
                    <button type="submit" name="signin" class="btn">Sign In</button>
                </form>
                <form id="signup-form" method="POST" style="display: none;">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="terms"> I agree to the Terms & Conditions</label>
                    </div>
                    <button type="submit" name="signup" class="btn">Create Account</button>
                </form>
                <p>or continue with</p>
                <div class="actions">
                    <button class="btn">🔵</button>
                    <button class="btn">📘</button>
                    <button class="btn">🍎</button>
                </div>
                <p>Protected by industry-standard encryption</p>
            </section>
        </div>
    </div>
</body>
</html>