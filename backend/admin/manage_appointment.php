<?php
// Include database connection
include('../includes/db_connection.php');

// Handle form submission for adding/editing appointments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = isset($_POST['appointment_id']) ? $_POST['appointment_id'] : null;
    $date = $_POST['date'];
    $status = $_POST['status'];
    $user_id = $_POST['user_id']; // Get user_id for the user who made the appointment
    $payment_id = $_POST['payment_id'];
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    // Add or update appointment depending on whether the appointment ID exists
    if ($appointment_id) {
        $stmt = $conn->prepare("UPDATE appointments SET date = ?, status = ?, user_id = ?, payment_id = ?, updated_at = ? WHERE id = ?");
        $stmt->bind_param("sssissi", $date, $status, $user_id, $payment_id, $updated_at, $appointment_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (date, status, user_id, payment_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $date, $status, $user_id, $payment_id, $created_at, $updated_at);
        $stmt->execute();
    }
    header("Location: manage_appointment.php");
    exit();
}

// Handle appointment deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_appointment.php");
    exit();
}

// Fetch appointments to display, including first name and last name as Booker
$result = $conn->query("SELECT appointments.id, appointments.date, appointments.status, appointments.payment_id, appointments.created_at, appointments.updated_at, 
                        CONCAT(users.lastname, ', ', users.firstname) AS booker
                        FROM appointments 
                        JOIN users ON appointments.user_id = users.id");

// Fetch all users for the dropdown list
$users_result = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) AS full_name FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
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
            <h1>Manage Appointments</h1>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <div class="appointment-list-container">
            <h2>Appointments List</h2>
            <button class="add-btn" onclick="openModal('add')">Add Appointment</button>

            <!-- Appointments Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Booker</th>
                        <th>Payment ID</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo $row['booker']; ?></td> <!-- Displaying Booker as Lastname, Firstname -->
                            <td><?php echo $row['payment_id']; ?></td>
                            <!-- Safely display created_at and updated_at with checks -->
                            <td><?php echo isset($row['created_at']) ? $row['created_at'] : 'N/A'; ?></td>
                            <td><?php echo isset($row['updated_at']) ? $row['updated_at'] : 'N/A'; ?></td>
                            <td>
                                <button class="edit-btn" onclick="openModal('edit', <?php echo $row['id']; ?>)">Edit</button>
                                <button class="delete-btn" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Appointment Modal -->
        <div id="appointmentModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3 id="modalTitle">Add Appointment</h3>
                <br>
                <form id="appointmentForm" action="manage_appointment.php" method="POST">
                    <input type="hidden" id="appointment_id" name="appointment_id" value="">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" required>
                    
                    <label for="status">Status</label>
                    <select name="status" id="status" required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="booked">Booked</option>
                    </select>

                    <label for="user_id">User</label>
                    <select name="user_id" id="user_id" required>
                        <?php while ($user = $users_result->fetch_assoc()) : ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo $user['full_name']; ?></option>
                        <?php endwhile; ?>
                    </select>

                    <label for="payment_id">Payment ID</label>
                    <input type="text" name="payment_id" id="payment_id" placeholder="Payment ID">

                    <button type="submit" class="submit-btn">Save Appointment</button>
                    <button type="button" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h3>Are you sure you want to delete this appointment?</h3>
            <button id="deleteConfirmBtn" onclick="deleteAppointment()">Yes, Delete</button>
            <button onclick="closeDeleteModal()">Cancel</button>
        </div>
    </div>

    <script>
        // Open the modal for adding or editing appointment
        function openModal(action, appointmentId = null) {
            document.getElementById("appointmentModal").style.display = "block";
            if (action === 'edit' && appointmentId) {
                // Fetch appointment data and pre-fill the form
                fetch('manage_appointment.php?edit_appointment_data=' + appointmentId)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('appointment_id').value = data.id;
                        document.getElementById('date').value = data.date;
                        document.getElementById('status').value = data.status;
                        document.getElementById('user_id').value = data.user_id;
                        document.getElementById('payment_id').value = data.payment_id;
                        document.getElementById('modalTitle').textContent = "Edit Appointment";
                    });
            } else {
                // Reset form for adding new appointment
                document.getElementById('appointmentForm').reset();
                document.getElementById('modalTitle').textContent = "Add Appointment";
            }
        }

        // Close the modal
        function closeModal() {
            document.getElementById("appointmentModal").style.display = "none";
        }

        // Confirm deletion of an appointment
        function confirmDelete(appointmentId) {
            document.getElementById("deleteModal").style.display = "block";
            document.getElementById("deleteConfirmBtn").onclick = function() {
                window.location.href = "manage_appointment.php?delete_id=" + appointmentId;
            };
        }

        // Close the delete confirmation modal
        function closeDeleteModal() {
            document.getElementById("deleteModal").style.display = "none";
        }
    </script>
</body>
</html>
