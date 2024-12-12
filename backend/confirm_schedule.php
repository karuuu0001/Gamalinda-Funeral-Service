<?php
session_start();
include('includes/db_connection.php');

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['date'])) {
        $selected_date = $_POST['date'];  // Get the selected date from POST

        // Store the selected date in the session
        $_SESSION['selected_date'] = $selected_date;

        // Check if appointment already exists for the selected date
        $appointment_check_query = "SELECT id FROM appointments WHERE date = ?";
        $stmt = $conn->prepare($appointment_check_query);
        $stmt->bind_param("s", $selected_date);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $appointment_row = $result->fetch_assoc();
            $appointment_id = $appointment_row['id'];
        } else {
            if (!isset($_SESSION['user_id'])) {
                die("Error: User not logged in.");
            }

            // Insert new appointment record
            $insert_appointment_query = "INSERT INTO appointments (date, status, user_id) VALUES (?, 'pending', ?)";
            $stmt = $conn->prepare($insert_appointment_query);
            $stmt->bind_param("si", $selected_date, $_SESSION['user_id']);
            $stmt->execute();
            $appointment_id = $stmt->insert_id;
        }

        // Ensure memorial details are provided
        if (isset($_POST['last_name'], $_POST['first_name'], $_POST['date_of_death'])) {
            $last_name = $_POST['last_name'];
            $first_name = $_POST['first_name'];
            $date_of_death = $_POST['date_of_death'];

            // Insert memorial details into the memorials table
            $insert_memorial_query = "INSERT INTO memorials (appointment_id, last_name, first_name, date_of_death) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_memorial_query);
            $stmt->bind_param("isss", $appointment_id, $last_name, $first_name, $date_of_death);
            $stmt->execute();

            // Store additional details in the session
            $_SESSION['appointment_id'] = $appointment_id;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['date_of_death'] = $date_of_death;

            // Redirect to the payment page
            header('Location: payment.php');
            exit;
        } else {
            /// echo "<p>Error: Memorial details are missing.</p>";
        }
    } else {
        echo "<p>Error: Appointment date is missing.</p>";
    }
} else {
    echo "<p>Error: Form not submitted correctly.</p>";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Appointment</title>
    <style>
        /* Custom CSS */
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

        input[type="text"], input[type="date"], input[type="submit"] {
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
    </style>
</head>
<body>

    <div class="container">
        <header>
            <h2>Confirm Your Appointment</h2>
            <p>You have selected the appointment date: <b><br><?php echo htmlspecialchars($selected_date ?? ''); ?></b></p>
            <p>Please enter the memorial details below:</p>
        </header>

        <!-- Memorial Details Form -->
        <form method="POST" action="confirm_schedule.php">
            <!-- Hidden fields to send the selected date -->
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($selected_date ?? ''); ?>">

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required><br><br>

            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required><br><br>

            <label for="date_of_death">Date of Death:</label>
            <input type="date" id="date_of_death" name="date_of_death" required><br><br>

            <input type="submit" value="Proceed to Payment">
        </form>
    </div>

</body>
</html>
