<?php
// Include database connection
include('../includes/db_connection.php');

// Handle form submission for adding/editing memorials
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the POST data exists before using it
    $memorial_id = isset($_POST['memorial_id']) ? $_POST['memorial_id'] : null;
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $appointment_id = isset($_POST['appointment_id']) ? $_POST['appointment_id'] : '';
    $date_of_death = isset($_POST['date_of_death']) ? $_POST['date_of_death'] : '';
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    // Add or update memorial record depending on whether the memorial ID exists
    if ($memorial_id) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE memorials SET last_name = ?, first_name = ?, appointment_id = ?, date_of_death = ?, updated_at = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $last_name, $first_name, $appointment_id, $date_of_death, $updated_at, $memorial_id);
        $stmt->execute();
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO memorials (last_name, first_name, appointment_id, date_of_death, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $last_name, $first_name, $appointment_id, $date_of_death, $created_at, $updated_at);
        $stmt->execute();
    }

    header("Location: memorials.php");
    exit();
}

// Handle memorial deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM memorials WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: memorials.php");
    exit();
}

// Fetch memorials to display
$result = $conn->query("SELECT * FROM memorials");

// Handle fetching memorial data for edit via GET
$memorial_data = null;
if (isset($_GET['edit_memorial_data'])) {
    $edit_id = $_GET['edit_memorial_data'];
    $stmt = $conn->prepare("SELECT * FROM memorials WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $memorial_data = $stmt->get_result()->fetch_assoc();

    // Return memorial data as JSON
    header('Content-Type: application/json');
    echo json_encode($memorial_data);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Memorials - Admin</title>
    <link rel="stylesheet" href="../css/manage/manage.css">
</head>
<body>
    <div class="back-to-dashboard-container">
        <a href="dashboard.php" class="back-to-dashboard-btn">
            Back to Dashboard
        </a>
    </div>

    <!-- Banner Section -->
    <header class="banner">
        <img src="../Assets/Image/banner.jpg" alt="Funeral Services" class="banner-image">
        <div class="banner-text">
            <h1>Memorial List</h1>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <div class="user-list-container">
            <h2>Memorial List</h2>
            <button class="add-btn" onclick="openModal('add')">Add</button>

            <!-- Memorial Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Appointment ID</th>
                        <th>Date of Death</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['last_name']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><?php echo $row['appointment_id']; ?></td>
                            <td><?php echo $row['date_of_death']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td><?php echo $row['updated_at']; ?></td>
                            <td>
                                <button class="edit-btn" onclick="openModal('edit', <?php echo $row['id']; ?>)">Edit</button>
                                <button class="delete-btn" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Memorial Modal -->
        <div id="userModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3 id="modalTitle">Add Memorial</h3>
                <br>
                <form id="memorialForm" action="memorials.php" method="POST">
                    <input type="hidden" id="memorial_id" name="memorial_id" value="<?php echo isset($memorial_data['id']) ? $memorial_data['id'] : ''; ?>">

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo isset($memorial_data['last_name']) ? htmlspecialchars($memorial_data['last_name']) : ''; ?>" required>

                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo isset($memorial_data['first_name']) ? htmlspecialchars($memorial_data['first_name']) : ''; ?>" required>

                    <label for="appointment_id">Appointment ID:</label>
                    <input type="text" id="appointment_id" name="appointment_id" value="<?php echo isset($memorial_data['appointment_id']) ? htmlspecialchars($memorial_data['appointment_id']) : ''; ?>" required>

                    <label for="date_of_death">Date of Death:</label>
                    <input type="date" id="date_of_death" name="date_of_death" value="<?php echo isset($memorial_data['date_of_death']) ? htmlspecialchars($memorial_data['date_of_death']) : ''; ?>" required>

                    <br><br>
                    <button type="submit"><?php echo isset($memorial_data) ? 'Save Changes' : 'Save Memorial'; ?></button>
                    <button type="button" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Open the add/edit memorial modal
        function openModal(action, id = null) {
            document.getElementById('memorialForm').reset();
            document.getElementById('modalTitle').textContent = action === 'add' ? 'Add Memorial' : 'Edit Memorial';

            // If editing an existing memorial, populate fields with current data
            if (action === 'edit' && id) {
                const modal = document.getElementById('userModal');
                modal.style.display = 'flex';

                // Fetch the memorial data for the given ID
                fetch('memorials.php?edit_memorial_data=' + id)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('memorial_id').value = data.id;
                        document.getElementById('last_name').value = data.last_name;
                        document.getElementById('first_name').value = data.first_name;
                        document.getElementById('appointment_id').value = data.appointment_id;
                        document.getElementById('date_of_death').value = data.date_of_death;
                    });
            } else {
                const modal = document.getElementById('userModal');
                modal.style.display = 'flex';
            }
        }

        // Close the modal
        function closeModal() {
            const modal = document.getElementById('userModal');
            modal.style.display = 'none';
        }

        // Confirm deletion of memorial
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this memorial?')) {
                window.location.href = 'memorials.php?delete_id=' + id;
            }
        }
    </script>
</body>
</html>
