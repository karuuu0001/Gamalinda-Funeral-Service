<?php
session_start();
include('includes/db_connection.php');

// Check if the necessary session variables are set
if (isset($_SESSION['appointment_id'], $_SESSION['last_name'], $_SESSION['first_name'], $_SESSION['date_of_death'], $_SESSION['selected_date'])) {
    // Fetch data from session
    $appointment_id = $_SESSION['appointment_id'];
    $last_name = $_SESSION['last_name'];
    $first_name = $_SESSION['first_name'];
    $date_of_death = $_SESSION['date_of_death'];
    $selected_date = $_SESSION['selected_date'];

    // The fixed cost for the service
    $cost = 5000.00;

} else {
    // If session variables are missing, redirect to confirm_schedule page
    header('Location: confirm_schedule.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        /* Style definitions */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 500px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            text-align: left;
            display: block;
            width: 100%;
        }
        input[type="text"], input[type="number"], input[type="date"], input[type="submit"] {
            width: 95%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border: none;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .summary {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 4px;
        }
        .summary b {
            color: #333;
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Black with opacity */
            overflow: auto;
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
        }

        .modal-header {
            font-size: 18px;
            font-weight: bold;
        }

        .modal-body {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .modal-footer {
            text-align: right;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        h3 {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Payment Summary</h2>
    <p>You are about to make a payment for your appointment. Please review the details below:</p>

    <div class="summary">
        <p><b>Appointment Date:</b> <?php echo htmlspecialchars($selected_date); ?></p>
        <p><b>Memorial Person Name:</b> <?php echo htmlspecialchars($last_name) . ", " . htmlspecialchars($first_name); ?></p>
        <p><b>Date of Death:</b> <?php echo htmlspecialchars($date_of_death); ?></p>
        <br>
        <p><b>Total Cost:</b> ₱5,000.00</p>
    </div>

    <!-- Fake Credit Card Form -->
    <h3>Enter Fake Credit Card Details</h3>
    <form method="POST" action="payment.php">
        <label for="card_number">Credit Card Number:</label>
        <input type="number" id="card_number" name="card_number" placeholder="4111 1111 1111 1111" required><br><br>

        <label for="card_expiry">Expiry Date:</label>
        <input type="date" id="card_expiry" name="card_expiry" required><br><br>

        <label for="card_cvc">CVC:</label>
        <input type="number" id="card_cvc" name="card_cvc" placeholder="123" required><br><br>

        <label for="amount">Amount to pay:</label>
        <input type="number" id="amount" name="amount_pay" placeholder="Amount to pay : ₱5,000. Pay ₱1,000 to reserve only" required><br><br>

        <input type="submit" value="Pay Now">
        <button href='index.php'>Cancel</button>
    </form>
</div>

<?php
// Handle the fake payment and insert data after the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_number'], $_POST['card_expiry'], $_POST['card_cvc'])) {
    // Get the amount to pay entered by the user
    $amount_to_pay = (float) $_POST['amount_pay']; 

    // Total cost for the service
    $total_cost = 5000.00; 

    // Calculate the balance
    $balance = $total_cost - $amount_to_pay;

    // Determine the payment status based on the amount entered
    $payment_status = ($amount_to_pay >= $total_cost) ? 'paid' : 'pending';

    // Get the user ID from the session (assuming user ID is stored in session)
    $user_id = $_SESSION['user_id'];  

    // Insert payment into the database
    $insert_payment_query = "INSERT INTO payments (user_id, appointment_id, amount, balance, status) 
                             VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_payment_query);
    $stmt->bind_param("iiids", $user_id, $appointment_id, $amount_to_pay, $balance, $payment_status);

    if ($stmt->execute()) {
        // Step 2: Get the payment ID and update the appointment table
        $payment_id = $stmt->insert_id;

        // Update the appointment table with the payment_id
        $update_appointment_query = "UPDATE appointments SET payment_id = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_appointment_query);
        $update_stmt->bind_param("ii", $payment_id, $appointment_id);
        $update_stmt->execute();

        // Step 3: Check if payment was partial and update appointment status
        if ($amount_to_pay > 1000 && $amount_to_pay < 4990) {
            // Mark the appointment as 'reserved' or 'pending'
            $update_status_query = "UPDATE appointments SET status = 'booked' WHERE id = ?";
            $update_status_stmt = $conn->prepare($update_status_query);
            $update_status_stmt->bind_param("i", $appointment_id);
            $update_status_stmt->execute();
        }

        // Show success modal with summary invoice
        echo "<script>
                window.onload = function() {
                    document.getElementById('paymentModal').style.display = 'block';
                }
              </script>";
    } else {
        // Handle error if payment insertion fails
        echo "<p>Error: Payment could not be processed. Please try again later.</p>";
    }
}
?>

<!-- Modal Popup -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('paymentModal').style.display='none'">&times;</span>
        <div class="modal-header">
            Payment Successful
        </div>
        <div class="modal-body">
        <?php
            // Calculate the balance
            $balance = $total_cost - $amount_to_pay;
            ?>
            <p>Your appointment has been successfully booked! Thank you for using our service.</p>
            <br><h3>Invoice Summary:</h3>
            <p><b>Appointment Date:</b> <?php echo htmlspecialchars($selected_date); ?></p>
            <p><b>Memorial Name:</b> <?php echo htmlspecialchars($first_name) . " " . htmlspecialchars($last_name); ?></p>
            <p><b>Date of Death:</b> <?php echo htmlspecialchars($date_of_death); ?></p>
            <br>
            <p><b>Total Cost:</b> ₱5,000.00</p>
            <p><b>Total Paid:</b> ₱<?php echo htmlspecialchars(number_format($amount_to_pay, 2)); ?></p>
            <p><b>Balance:</b> ₱<?php echo htmlspecialchars(number_format($balance, 2)); ?></p>
        </div>
        <div class="modal-footer">
            <button onclick="window.location.href='../index.php'">OK</button>
        </div>
    </div>
</div>

</body>
</html>