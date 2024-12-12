<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get user information from session
$user_name = $_SESSION['username']; // Get the username from session
$user_role = $_SESSION['role']; // Get the role (Admin/User) from session

// Check if user is an Admin
if ($user_role !== 'Admin') {
    echo "Access denied. You are not authorized to view this page.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard - Funeral Services</title>
    <style>
        /* Global Styles */
        :root {
        --primary-color: #4f46e5; /* Indigo */
        --secondary-color: #818cf8; /* Soft Blue */
        --accent-color: #facc15; /* Yellow */
        --bg-color: #f8fafc; /* Light Gray */
        --text-color: #1e293b; /* Dark Gray */
        --font-family: 'Inter', sans-serif;
        }

        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        }

        body {
        font-family: var(--font-family);
        background-color: var(--bg-color);
        color: var(--text-color);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        }

        /* Header Styles */
        header.banner {
        width: 100%;
        position: relative;
        }

        header.banner img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        opacity: 0.8;
        }

        header .banner-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        text-align: center;
        }

        header .banner-text h1 {
        font-size: 2.5rem;
        margin: 0;
        }

        /* Container Styling */
        .container {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
        margin: 2rem auto;
        text-align: center;
        }

        .container h2 {
        font-size: 1.8rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        }

        .container p {
        font-size: 1rem;
        color: var(--text-color);
        margin-bottom: 1.5rem;
        }

        .container h3 {
        font-size: 1.5rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
        }

        /* List Styles */
        .container ul {
        list-style-type: none;
        padding: 0;
        }

        .container ul li {
        margin: 1rem 0;
        }

        .container ul li a {
        display: inline-block;
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
        color: white;
        background: var(--primary-color);
        text-decoration: none;
        border-radius: 5px;
        transition: background 0.3s ease, transform 0.3s ease;
        }

        .container ul li a:hover {
        background: var(--accent-color);
        transform: translateY(-3px);
        }

        /* Logout Button */
        .container ul li a.logout {
        background: crimson;
        }

        .container ul li a.logout:hover {
        background: darkred;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
        header .banner-text h1 {
            font-size: 1.8rem;
        }

        .container {
            padding: 1.5rem;
        }

        .container ul li a {
            font-size: 0.9rem;
            padding: 0.6rem 1rem;
        }
        }

    </style>

</head>
<body>
    <header class="banner">
        <img src="../Assets/Image/banner.jpg" alt="Funeral Services" class="banner-image">
        <div class="banner-text">
            <h1>Welcome to the Admin Dashboard</h1>
        </div>
    </header>

    <div class="container">
        <h2>Welcome, <?php echo $user_name; ?>!</h2>
        <br>
        <h3>Admin Options</h3>
        <ul>
            <li><a href="manage_user.php">View All Users</a></li>
            <li><a href="manage_appointment.php">Manage Appointment</a></li>
            <li><a href="manage_payments.php">Payments</a></li>
            <li><a href="Memorials.php">Memorial List</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </div>
</body>
</html>
