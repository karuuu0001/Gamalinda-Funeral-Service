<?php
// Include database connection
include('../includes/db_connection.php');

// Handle form submission for adding/editing users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $username = $_POST['username'];
    $firstname = $_POST['firstname']; // Updated field name
    $lastname = $_POST['lastname']; // Updated field name
    $phone = $_POST['phone'];
    $email = $_POST['email']; // Add email field
    $password = $_POST['password'];
    $role = $_POST['role'];
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    // If password is empty, use the existing password for edit
    if ($password == '') {
        if ($user_id) {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            $password = $user_data['password']; // Retain current password
        }
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $password = $hashed_password;
    }

    // Add or update user depending on whether the user ID exists
    if ($user_id) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, firstname = ?, lastname = ?, phone = ?, email = ?, password = ?, role = ?, updated_at = ? WHERE id = ?");
        $stmt->bind_param("ssssssssi", $username, $firstname, $lastname, $phone, $email, $password, $role, $updated_at, $user_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, firstname, lastname, phone, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $username, $firstname, $lastname, $phone, $email, $password, $role, $created_at, $updated_at);
        $stmt->execute();
    }
    header("Location: manage_user.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_user.php");
    exit();
}

// Fetch users to display
$result = $conn->query("SELECT * FROM users");

// Handle fetching user data for edit via GET
$user_data = null;
if (isset($_GET['edit_user_data'])) {
    $edit_id = $_GET['edit_user_data'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $user_data = $stmt->get_result()->fetch_assoc();

    // Return user data as JSON
    header('Content-Type: application/json');
    echo json_encode($user_data);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
            <h1>Manage Users</h1>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <div class="user-list-container">
            <h2>Users List</h2>
            <button class="add-btn" onclick="openModal('add')">Add User</button>

            <!-- User Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Phone</th>
                        <th>Email</th> <!-- Added email column -->
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['firstname']; ?></td>
                            <td><?php echo $row['lastname']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['email']; ?></td> <!-- Display email -->
                            <td><?php echo $row['role']; ?></td>
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

        <!-- Add/Edit User Modal -->
        <div id="userModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3 id="modalTitle">Add User</h3>
                <br>
                <form id="userForm" action="manage_user.php" method="POST">
                    <input type="hidden" id="user_id" name="user_id" value="<?php echo isset($user_data['id']) ? $user_data['id'] : ''; ?>">

                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($user_data['username']) ? htmlspecialchars($user_data['username']) : ''; ?>" required>

                    <label for="firstname">First Name:</label>
                    <input type="text" id="firstname" name="firstname" value="<?php echo isset($user_data['firstname']) ? htmlspecialchars($user_data['firstname']) : ''; ?>" required>

                    <label for="lastname">Last Name:</label>
                    <input type="text" id="lastname" name="lastname" value="<?php echo isset($user_data['lastname']) ? htmlspecialchars($user_data['lastname']) : ''; ?>" required>

                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo isset($user_data['phone']) ? htmlspecialchars($user_data['phone']) : ''; ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="input-field" value="<?php echo isset($user_data['email']) ? htmlspecialchars($user_data['email']) : ''; ?>" required>

                    <label for="password">Password:</label>
                    <div class="password-field">
                        <input type="password" id="password" name="password">
                    </div>

                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="User" <?php echo isset($user_data) && $user_data['role'] == 'User' ? 'selected' : ''; ?>>User</option>
                        <option value="Admin" <?php echo isset($user_data) && $user_data['role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    <br>
                    <br>
                    <button type="submit"><?php echo isset($user_data) ? 'Save Changes' : 'Save User'; ?></button>
                    <button type="button" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Open the add/edit user modal
        function openModal(action, id = null) {
            document.getElementById('userForm').reset();
            document.getElementById('modalTitle').textContent = action === 'add' ? 'Add User' : 'Edit User';

            // If editing an existing user, populate fields with current data
            if (action === 'edit' && id) {
                // Open the modal
                const modal = document.getElementById('userModal');
                modal.style.display = 'flex';

                // Fetch the user data for the given ID
                fetch('manage_user.php?edit_user_data=' + id)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('user_id').value = data.id;
                        document.getElementById('username').value = data.username;
                        document.getElementById('firstname').value = data.firstname;
                        document.getElementById('lastname').value = data.lastname;
                        document.getElementById('phone').value = data.phone;
                        document.getElementById('email').value = data.email; // Populate email
                        document.getElementById('role').value = data.role;
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

        // Toggle the password visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
        }

        // Confirm deletion of user
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = 'manage_user.php?delete_id=' + id;
            }
        }
    </script>
</body>
</html>
