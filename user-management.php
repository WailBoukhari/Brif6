<?php
include 'db_cnx.php'; // Include your database connection file
session_start();

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from the database
$sql = "SELECT * FROM Users";
$result = $conn->query($sql);

$users = [];

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    } else {
        echo "Error: No users found in the database";
    }
} else {
    echo "Error executing query to retrieve users: " . $conn->error;
}
// Handle form submission for editing user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editUserId'])) {
    $editUserId = $_POST['editUserId'];
    $editUsername = $_POST['editUsername'];

    // Check if the email and password are set and not empty before using them
    $editEmail = isset($_POST['editEmail']) ? $_POST['editEmail'] : null;
    $editPassword = isset($_POST['editPassword']) ? $_POST['editPassword'] : null;

    // Check if role and verified are set
    $editRole = isset($_POST['editRole']) ? $_POST['editRole'] : null;
    $editVerified = isset($_POST['editVerified']) ? $_POST['editVerified'] : null;

    // Validate that email is not empty before updating
    if (empty($editEmail)) {
        echo "Error: Email cannot be empty.";
    } else {
        // Check if the new email already exists in the database
        $checkEmailSql = "SELECT COUNT(*) as count FROM Users WHERE Email = '$editEmail' AND UserID <> $editUserId";
        $emailResult = $conn->query($checkEmailSql);

        if ($emailResult) {
            $emailCount = $emailResult->fetch_assoc()['count'];

            if ($emailCount > 0) {
                echo "Error: Email address already exists. Please use a different email.";
            } else {
                // Update user data in the database
                $updateSql = "UPDATE Users SET Username='$editUsername', Email='$editEmail', Password='$editPassword', Role='$editRole', Verified='$editVerified' WHERE UserID=$editUserId";

                if ($conn->query($updateSql) === TRUE) {
                    echo "User data updated successfully";
                    header("Location: user-management.php");
                    exit();
                } else {
                    echo "Error updating user data: " . $conn->error;
                }
            }
        } else {
            echo "Error executing query to check email existence: " . $conn->error;
        }
    }
}


// Handle form submission for adding a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUser'])) {
    $addUsername = $_POST['addUsername'];
    $addEmail = $_POST['addEmail'];
    $addPassword = $_POST['addPassword'];
    $addRole = $_POST['addRole'];
    $addVerified = isset($_POST['addVerified']) ? $_POST['addVerified'] : null;

    // Check if the email already exists in the database
    $checkEmailSql = "SELECT COUNT(*) as count FROM Users WHERE Email = '$addEmail'";
    $emailResult = $conn->query($checkEmailSql);

    if ($emailResult) {
        $emailCount = $emailResult->fetch_assoc()['count'];

        if ($emailCount > 0) {
            echo "Error: Email address already exists. Please use a different email.";
        } else {
            // Insert new user into the database
            $insertSql = "INSERT INTO Users (Username, Email, Password, Role, Verified) VALUES ('$addUsername', '$addEmail', '$addPassword', '$addRole', '$addVerified')";

            if ($conn->query($insertSql) === TRUE) {
                echo "New user '$addUsername' added successfully";
                header("Location: user-management.php");
                exit();
            } else {
                echo "Error adding new user: " . $conn->error;
            }
        }
    } else {
        echo "Error executing query to check email existence: " . $conn->error;
    }
}

// Handle form submission for deleting a user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUserId'])) {
    $deleteUserId = $_POST['deleteUserId'];

    // Delete user from the database
    $deleteSql = "DELETE FROM Users WHERE UserID=$deleteUserId";

    if ($conn->query($deleteSql) === TRUE) {
        echo "User deleted successfully";
        header("Location: user-management.php");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- fontawesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha256-hk1J8HZqEW/p7zC0xjYYr4EhGtYszmJdz21pKBC7ROU=" crossorigin="anonymous" />
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4">User Management</h2>

        <!-- Display Users -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo $user['UserID']; ?></td>
                    <td><?php echo $user['Username']; ?></td>
                    <td><?php echo $user['Email']; ?></td>
                    <td><?php echo $user['Password']; ?></td>
                    <td><?php echo $user['Role']; ?></td>
                    <td><?php echo $user['Verified'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <button type="button" class="btn btn-primary btn"
                            onclick="showUpdateModal(<?php echo $user['UserID']; ?>)">
                            Edit
                        </button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="deleteUserId" value="<?php echo $user['UserID']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this user?');">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <!-- Update User Modal -->
                <div id="updateModal<?php echo $user['UserID']; ?>" class="modal" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateModalLabel<?php echo $user['UserID']; ?>">Update User
                                </h5>
                            </div>
                            <div class="modal-body">
                                <form method="post">
                                    <input type="hidden" name="editUserId" value="<?php echo $user['UserID']; ?>">
                                    <div class="mb-3">
                                        <label for="editUsername<?php echo $user['UserID']; ?>"
                                            class="form-label">Username:</label>
                                        <input type="text" class="form-control"
                                            id="editUsername<?php echo $user['UserID']; ?>" name="editUsername"
                                            value="<?php echo $user['Username']; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmail<?php echo $user['UserID']; ?>"
                                            class="form-label">Email:</label>
                                        <input type="text" class="form-control"
                                            id="editEmail<?php echo $user['UserID']; ?>" name="editEmail"
                                            value="<?php echo $user['Email']; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editPassword<?php echo $user['UserID']; ?>"
                                            class="form-label">Password:</label>
                                        <input type="text" class="form-control"
                                            id="editPassword<?php echo $user['UserID']; ?>" name="editPassword"
                                            value="<?php echo $user['Password']; ?>">

                                    </div>
                                    <div class="mb-3">
                                        <label for="editRole<?php echo $user['UserID']; ?>"
                                            class="form-label">Role:</label>
                                        <select class="form-select selectpicker"
                                            id="editRole<?php echo $user['UserID']; ?>" name="editRole"
                                            data-live-search="true">
                                            <option value="admin"
                                                <?php echo ($user['Role'] === 'Admin') ? 'selected' : ''; ?>>Admin
                                            </option>
                                            <option value="user"
                                                <?php echo ($user['Role'] === 'User') ? 'selected' : ''; ?>>User
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editVerified<?php echo $user['UserID']; ?>"
                                            class="form-label">Verified:</label>
                                        <select class="form-select" id="editVerified<?php echo $user['UserID']; ?>"
                                            name="editVerified">
                                            <option value="1"
                                                <?php echo ($user['Verified'] === 'Yes') ? 'selected' : ''; ?>>Yes
                                            </option>
                                            <option value="0"
                                                <?php echo ($user['Verified'] === 'No') ? 'selected' : ''; ?>>No
                                            </option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="button" class="btn btn-success btn-sm" onclick="showAddModal()">
            Add
        </button>
        <!-- Add User  -->
        <div id="addModal" class="modal" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add Product</h5>
                    </div>
                    <div class="modal-body">
                        <!-- Add your form fields for adding a new product -->
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="addUsername" class="form-label">Username:</label>
                                <input type="text" class="form-control" id="addUsername" name="addUsername" required>
                            </div>
                            <div class="mb-3">
                                <label for="addEmail<?php echo $product['UserID']; ?>" class="form-label">Email:</label>
                                <input type="text" class="form-control" id="addEmail<?php echo $product['UserID']; ?>"
                                    name="addEmail" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="addPassword<?php echo $product['UserID']; ?>"
                                    class="form-label">Password:</label>
                                <input type="text" class="form-control"
                                    id="addPassword<?php echo $product['UserID']; ?>" name="addPassword" value=""
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="addRole" class="form-label">Category:</label>
                                <select class="form-select" id="addRole" name="addRole" required>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="addVerified" class="form-label">Category:</label>
                                <select class="form-select" id="addVerified" name="addVerified" required>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                    <!-- Add more categories as needed -->
                                </select>
                            </div>


                            <button type="submit" name="addUser" class="btn btn-success">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- You can follow a similar modal structure as in the product-management.php file -->

    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha384-GLhlTQ8iS6LHs pierced YWR1u7kDToSf5NV9In1EJ+sKtwEVR5EJFdm2i5EG98vUuwjA"
        crossorigin="anonymous"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    <script>
    // Function to show the update modal
    function showUpdateModal(UserID) {
        var modal = document.getElementById('updateModal' + UserID);
        modal.style.display = 'block';
    }

    // Function to show the add modal
    function showAddModal() {
        var modal = document.getElementById('addModal');
        modal.style.display = 'block';
    }

    // Close modals when clicking outside the modal
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    };
    $(document).ready(function() {
        $('.selectpicker').selectpicker();
    });
    </script>

</body>

</html>