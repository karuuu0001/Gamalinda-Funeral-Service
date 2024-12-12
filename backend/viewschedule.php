<?php
session_start();
require_once 'includes/db_connection.php'; // Updated path to db_connection.php

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

$user_id = $_SESSION['user_id'];

// Query to fetch the required data
$sql = "
    SELECT 
        a.date AS appointment_date,
        m.id AS memorial_id,
        CONCAT(m.first_name, ', ', m.last_name) AS memorial_name,
        a.status AS appointment_status,
        p.status AS payment_status,
        p.amount AS payment_amount, 
        p.balance AS payment_balance,
        a.created_at AS appointment_created_at
    FROM 
        appointments a
    JOIN 
        memorials m ON a.id = m.appointment_id
    LEFT JOIN 
        payments p ON a.id = p.appointment_id
    WHERE 
        a.user_id = ? 
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <style>
        /* Reset some default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
        }

        /* Container */
        .container {
            width: 98%;
            margin: 0 auto;
            padding: 40px;
            background-color: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 40px;
        }

        /* Header */
        header {
            text-align: center;
            margin-bottom: 30px;
            position: relative; /* Added to position the back button */
        }

        header h1 {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 10px;
        }

        header p {
            font-size: 1.2em;
            color: #777;
        }

        /* Back Button */
        .back-btn {
            position: absolute;
            top: 5px;
            left: 20px;
            padding: 10px 20px;
            background-color: #5c6bc0;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            width: 100px;
            
        }

        .back-btn:hover {
            background-color: #4f5b8a;
        }

        /* Table Styles */
        .appointments-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #ddd; 
        }

        .appointments-table th, .appointments-table td {
            padding: 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd; 
        }

        .appointments-table th {
            background-color: #5c6bc0;
            color: white;
            font-size: 1.1em;
            font-weight: bold;
        }

        .appointments-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .appointments-table tr:hover {
            background-color: #f1f1f1;
        }

        .appointments-table td {
            font-size: 1.1em;
            color: #555;
        }

        /* Highlight Statuses */
        .status {
            font-weight: bold;
            padding: 2px 10px;
            border-radius: 5px;
            text-align: center;
            display: flex;
            justify-content: center; 
            align-items: center; 
            width: 100%;
            
        }

        /* Styling for specific statuses */
        .status.pending {
            background-color: #f5c542;
            color: #fff;
        }

        .status.approved {
            background-color: #4caf50;
            color: #fff;
        }

        .status.booked {
            background-color: #2196f3;
            color: #fff;
        }

        .status.paid {
            background-color: #4caf50;
            color: #fff;
        }

        .status.failed {
            background-color: #f44336;
            color: #fff;
        }


        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            header h1 {
                font-size: 2em;
            }

            .appointments-table th, .appointments-table td {
                padding: 10px;
            }

            .appointments-table td {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <button class="back-btn" onclick="window.location.href='../index.php#login';">↶ Back</button>
            <h1>My Appointments</h1>
            <p>Below are your appointment details including memorial and payment statuses.</p>
        </header>

        <div class="appointments-table">
            <table>
                <thead>
                    <tr>
                        <th>Appointment Date</th>
                        <th>Memorial ID</th>
                        <th>Name of Memorial Person</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['memorial_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['memorial_name']); ?></td>
                                <td><span class="status <?php echo strtolower($row['appointment_status']); ?>"><?php echo htmlspecialchars($row['appointment_status']); ?></span></td>
                                <td>
                                    <span class="status <?php echo strtolower($row['payment_status']); ?>">
                                        <?php echo htmlspecialchars($row['payment_status']); ?>
                                    </span>
                                    <?php 
                                        if (strtolower($row['payment_status']) === 'pending' && $row['payment_balance'] > 0) {
                                            echo "<small>Please pay the remaining balance of ₱" . htmlspecialchars($row['payment_balance']) . ".</small>";
                                        } elseif (strtolower($row['payment_status']) === 'pending') {
                                            echo "<small>Please pay the appointment fee.</small>";
                                        }
                                    ?>
                                </td>
                                <td>₱5,000</td>
                                <td><?php echo "₱" . number_format($row['payment_amount']); ?></td>
                                <td><?php echo "₱" . number_format($row['payment_balance']); ?></td>
                                <td><?php echo htmlspecialchars($row['appointment_created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #888;">No appointments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
