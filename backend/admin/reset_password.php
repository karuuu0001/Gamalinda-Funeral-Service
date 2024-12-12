<?php
session_start();
include('../includes/db_connection.php'); // Adjust the path for your database connection

// Initialize error message and success message
$error_message = '';
$success_message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];

    // Check if the password field is not empty
    if (empty($new_password)) {
        $error_message = "Please enter a new password.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Get the logged-in user ID (you could modify this to reset for any specific user if needed)
        $user_id = $_SESSION['user_id']; 

        // Update the user's password in the database
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);

        // Execute the query
        if ($stmt->execute()) {
            $success_message = "Your password has been reset successfully.";
        } else {
            $error_message = "Error resetting the password. Please try again.";
        }

        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password - Funeral Services</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header class="banner">
        <img src="..Assets/Image/banner.jpg" alt="Funeral Services" class="banner-image">
        <div class="banner-text">
            <h1>Reset Password</h1>
        </div>
    </header>

    <div class="container">
        <h2>Reset Your Password</h2>

        <?php if ($error_message): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <form action="reset_password.php" method="POST">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>
            <button type="submit" class="btn-reset">Reset Password</button>
        </form>

        <p><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html>
