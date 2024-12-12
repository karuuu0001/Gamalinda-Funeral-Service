<?php
session_start(); // Ensure session is started

include('includes/db_connection.php');

// Initialize variables for error/success messages
$error_message = '';
$reset_message = '';
$show_forgot_password = false; // Flag to control "Forgot Password" form visibility
$show_new_password_form = false; // Flag to control "New Password" form visibility

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables for the logged-in user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on the user role
        if ($_SESSION['role'] === 'Admin') {
            // Redirect to the admin dashboard
            header("Location: admin/dashboard.php");
            exit;
        } else {
            // Redirect to the regular user homepage
            header("Location: ../index.php#login");
            exit;
        }
    } else {
        $error_message = "Invalid username or password!";
    }
}

// Handle Forgot Password form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $forgot_username = trim($_POST['forgot_username']);

    if (!empty($forgot_username)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $forgot_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['reset_username'] = $forgot_username;
            $reset_message = "A password reset link has been sent to your email (just a simulation).<br><br> Please enter your new password below.";
            $show_new_password_form = true; // Show "New Password" form
            $show_forgot_password = false; // Hide "Forgot Password" form
        } else {
            $reset_message = "Username not found. Please check your username.";
        }
    } else {
        $reset_message = "Username field cannot be empty.";
    }
}

// Handle new password submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password_submit'])) {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password === $confirm_password) {
        if (!empty($_SESSION['reset_username'])) {
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $new_password_hashed, $_SESSION['reset_username']);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $reset_message = "Your password has been reset successfully!";
                unset($_SESSION['reset_username']); // Clear session data
                $show_new_password_form = false; // Hide the "New Password" form
            } else {
                $reset_message = "Error updating password. Please try again later.";
            }
        } else {
            $reset_message = "Session error. Please restart the password reset process.";
        }
    } else {
        $reset_message = "Passwords do not match. Please try again.";
        $show_new_password_form = true; // Keep the "New Password" form visible
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="css/userlogin/userlogin.css">
</head>
<body>
    <div class="container <?php if ($show_forgot_password) echo 'forgot-password-visible'; ?> <?php if ($show_new_password_form) echo 'new-password-visible'; ?>" id="form-container">
        <!-- Login Form -->
        <div class="login-form">
            <h2>User Login</h2>
            <?php if (!empty($error_message)) { echo '<p class="error">' . $error_message . '</p>'; } ?>
            <form action="" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <div class="forgot-password-link">
                <a href="javascript:void(0);" onclick="toggleForgotPasswordForm()">Forgot Password?</a>
            </div>
            <div class="form-footer">
                <p>Don't have an account? <a href="userregister.php">Register here</a></p>
            </div>
        </div>

        <!-- Forgot Password Form -->
        <div class="forgot-password-form">
            <h2>Forgot Password</h2>
            <?php if (!empty($reset_message)) { echo '<p class="message">' . $reset_message . '</p>'; } ?>
            <form action="" method="POST">
                <label for="forgot_username">Enter your username:</label>
                <input type="text" id="forgot_username" name="forgot_username" required>
                <button type="submit" name="forgot_password">Reset Password</button>
            </form>
            <div class="form-footer">
                <p><a href="javascript:void(0);" onclick="toggleForgotPasswordForm()">Back to Login</a></p>
            </div>
        </div>

        <!-- New Password Form -->
        <div class="new-password-form">
            <h2>Set New Password</h2>
            <?php if (!empty($reset_message)) { echo '<p class="message">' . $reset_message . '</p>'; } ?>
            <form action="" method="POST">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <button type="submit" name="new_password_submit">Submit New Password</button>
            </form>
        </div>
    </div>

    <script>
        // Function to toggle between login and forgot password forms
        function toggleForgotPasswordForm() {
            var container = document.getElementById('form-container');
            container.classList.toggle('forgot-password-visible');
        }
    </script>
</body>
</html>
