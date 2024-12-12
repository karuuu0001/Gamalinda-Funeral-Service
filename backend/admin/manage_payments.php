<?php
// Include database connection
include('../includes/db_connection.php');

// Handle form submission for adding/editing payments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = isset($_POST['payment_id']) ? (int)$_POST['payment_id'] : null;
    $user_id = (int)$_POST['user_id'];
    $appointment_id = (int)$_POST['appointment_id'];
    $payment_date = $_POST['payment_date'];
    $amount = $_POST['amount'];
    $balance = $_POST['balance'];
    $status = $_POST['status'];
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    $error_message = '';

    // Check if the appointment_id exists in the appointments table
    $check_appointment = $conn->prepare("SELECT id FROM appointments WHERE id = ?");
    if (!$check_appointment) {
        die("Prepare failed: " . $conn->error);
    }
    $check_appointment->bind_param("i", $appointment_id);
    $check_appointment->execute();
    $result_appointment = $check_appointment->get_result();

    // Check if the user exists
    $check_user = $conn->prepare("SELECT id FROM users WHERE id = ?");
    if (!$check_user) {
        die("Prepare failed: " . $conn->error);
    }
    $check_user->bind_param("i", $user_id);
    $check_user->execute();
    $result_user = $check_user->get_result();

    // If either user or appointment_id doesn't exist, store an error message
    if ($result_appointment->num_rows === 0) {
        $error_message = "Appointment ID not found!";
    }
    
    if ($result_user->num_rows === 0) {
        if (!empty($error_message)) {
            $error_message .= " and "; 
        }
        $error_message .= "User ID not found!";
    }

    // If no errors, proceed with adding/updating payment
    if (empty($error_message)) {
        if ($payment_id) {
            $stmt = $conn->prepare("UPDATE payments SET user_id = ?, appointment_id = ?, payment_date = ?, amount = ?, balance = ?, status = ?, updated_at = ? WHERE id = ?");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("iisdsssi", $user_id, $appointment_id, $payment_date, $amount, $balance, $status, $updated_at, $payment_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO payments (user_id, appointment_id, payment_date, amount, balance, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("iisdssss", $user_id, $appointment_id, $payment_date, $amount, $balance, $status, $created_at, $updated_at);
            $stmt->execute();
        }
        echo json_encode(['success' => true]); // Return success response for AJAX
        exit();
    } else {
        echo json_encode(['success' => false, 'error_message' => $error_message]); // Return error message for AJAX
        exit();
    }
}

// Handle payment deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM payments WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_payments.php");
    exit();
}

// Fetch payments to display
$result = $conn->query("SELECT * FROM payments");

// Handle fetching payment data for edit via GET
if (isset($_GET['edit_payment_data'])) {
    $edit_id = (int)$_GET['edit_payment_data'];
    $stmt = $conn->prepare("SELECT * FROM payments WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $payment_data = $stmt->get_result()->fetch_assoc();

    // Return payment data as JSON
    header('Content-Type: application/json');
    echo json_encode($payment_data);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments - Admin</title>
    <link rel="stylesheet" href="../css/manage/manage.css">
</head>
<body>
    <div class="back-to-dashboard-container">
        <a href="dashboard.php" class="back-to-dashboard-btn">Back to Dashboard</a>
    </div>

    <!-- Banner Section -->
    <header class="banner">
        <img src="../Assets/Image/banner.jpg" alt="Funeral Services" class="banner-image">
        <div class="banner-text">
            <h1>Manage Payments</h1>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <div class="payment-list-container">
            <h2>Payments List</h2>
            <button class="add-btn" onclick="openModal('add')">Add Payment</button>

            <!-- Payments Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Appointment ID</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo $row['appointment_id']; ?></td>
                            <td><?php echo $row['payment_date']; ?></td>
                            <td><?php echo $row['amount']; ?></td>
                            <td><?php echo $row['balance']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <button class="edit-btn" onclick="openModal('edit', <?php echo $row['id']; ?>)">Edit</button>
                                <button class="delete-btn" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Payment Modal -->
        <div id="paymentModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3 id="modalTitle">Add Payment</h3>
                <p id="error-message" style="color: red;"></p>
                <form id="paymentForm" action="manage_payments.php" method="POST" onsubmit="submitForm(event)">
                    <input type="hidden" id="payment_id" name="payment_id">
                    <label for="user_id">User ID:</label>
                    <input type="number" id="user_id" name="user_id" required>
                    <label for="appointment_id">Appointment ID:</label>
                    <input type="number" id="appointment_id" name="appointment_id" required>
                    <label for="payment_date">Payment Date:</label>
                    <input type="datetime-local" id="payment_date" name="payment_date" required>
                    <label for="amount">Amount:</label>
                    <input type="text" id="amount" name="amount" required>
                    <label for="balance">Balance:</label>
                    <input type="text" id="balance" name="balance" required>
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                    </select>
                    <br><br>
                    <button type="submit">Save Payment</button>
                    <button type="button" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(action, id = null) {
            document.getElementById('paymentForm').reset();
            document.getElementById('modalTitle').textContent = action === 'add' ? 'Add Payment' : 'Edit Payment';
            document.getElementById('error-message').textContent = '';
            if (action === 'edit' && id) {
                const modal = document.getElementById('paymentModal');
                modal.style.display = 'flex';
                fetch('manage_payments.php?edit_payment_data=' + id)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('payment_id').value = data.id;
                        document.getElementById('user_id').value = data.user_id;
                        document.getElementById('appointment_id').value = data.appointment_id;
                        const formattedDate = data.payment_date.substring(0, 16).replace(' ', 'T');
                        document.getElementById('payment_date').value = formattedDate;
                        document.getElementById('amount').value = data.amount;
                        document.getElementById('balance').value = data.balance;
                        document.getElementById('status').value = data.status;
                    });
            } else {
                document.getElementById('paymentModal').style.display = 'flex';
            }
        }
        function closeModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this payment?')) {
                window.location.href = 'manage_payments.php?delete_id=' + id;
            }
        }
        function submitForm(event) {
            event.preventDefault();
            const form = document.getElementById('paymentForm');
            const formData = new FormData(form);
            fetch('manage_payments.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else {
                    document.getElementById('error-message').textContent = data.error_message;
                }
            });
        }
    </script>
</body>
</html>
