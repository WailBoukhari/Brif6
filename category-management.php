<?php
include 'db_cnx.php'; // Include your database connection file
session_start();

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding a new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCategory'])) {
    $categoryName = $_POST['categoryName'];

    // Check if the category already exists in the database
    $checkCategorySql = "SELECT COUNT(*) as count FROM Categories WHERE CategoryName = '$categoryName'";
    $categoryResult = $conn->query($checkCategorySql);

    if ($categoryResult) {
        $categoryCount = $categoryResult->fetch_assoc()['count'];

        if ($categoryCount > 0) {
            echo "Error: Category already exists. Please use a different category name.";
        } else {
            // Insert new category into the database
            $insertCategorySql = "INSERT INTO Categories (CategoryName) VALUES ('$categoryName')";

            if ($conn->query($insertCategorySql) === TRUE) {
                echo "New category '$categoryName' added successfully";
                header("Location: category-management.php");
                exit();
            } else {
                echo "Error adding new category: " . $conn->error;
            }
        }
    } else {
        echo "Error executing query to check category existence: " . $conn->error;
    }
}

// Fetch categories from the database
$sql = "SELECT * FROM Categories";
$result = $conn->query($sql);

$categories = [];

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    } else {
        echo "Error: No categories found in the database";
    }
} else {
    echo "Error executing query to retrieve categories: " . $conn->error;
}

// Handle form submission for deleting a category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteCategoryId'])) {
    $deleteCategoryId = $_POST['deleteCategoryId'];

    // Delete category from the database
    $deleteCategorySql = "DELETE FROM Categories WHERE CategoryID=$deleteCategoryId";

    if ($conn->query($deleteCategorySql) === TRUE) {
        echo "Category deleted successfully";
        header("Location: category-management.php");
        exit();
    } else {
        echo "Error deleting category: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- fontawesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha256-hk1J8HZqEW/p7zC0xjYYr4EhGtYszmJdz21pKBC7ROU=" crossorigin="anonymous" />

    <title>Category Management</title>
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4">Category Management</h2>
        <!-- Display Categories -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($categories as $category) : ?>
                    <tr>
                        <td><?php echo $category['CategoryID']; ?></td>
                        <td><?php echo $category['CategoryName']; ?></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" onclick="showEditModal(<?php echo $category['CategoryID']; ?>)">
                                Edit
                            </button>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="deleteCategoryId" value="<?php echo $category['CategoryID']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="button" class="btn btn-success btn-sm" onclick="showAddModal()">
            Add
        </button>
        <!-- Add Category Modal -->
        <div id="addModal" class="modal" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                    </div>
                    <div class="modal-body">
                        <!-- Add your form fields for adding a new category -->
                        <form method="post">
                            <div class="mb-3">
                                <label for="categoryName" class="form-label">Category Name:</label>
                                <input type="text" class="form-control" id="categoryName" name="categoryName" required>
                            </div>
                            <button type="submit" name="addCategory" class="btn btn-success">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Edit Category Modal -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <input type="hidden" id="editCategoryId" name="editCategoryId">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Category Name:</label>
                            <input type="text" class="form-control" id="editCategoryName" name="editCategoryName" required>
                        </div>
                        <button type="submit" name="editCategory" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha384-GLhlTQ8iS6LHs pierced YWR1u7kDToSf5NV9In1EJ+sKtwEVR5EJFdm2i5EG98vUuwjA" crossorigin="anonymous"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script>
        function showEditModal(categoryId) {
            // Fetch the category name based on the categoryId and update the modal
            // You can use JavaScript/AJAX to fetch the category name from the server
            var categoryName = "<?php echo $categories[0]['CategoryName']; ?>"; // Replace with actual category name

            // Set values in the modal
            document.getElementById('editCategoryId').value = categoryId;
            document.getElementById('editCategoryName').value = categoryName;

            // Display the modal
            var modal = document.getElementById('editModal');
            modal.style.display = 'block';
        }
    </script>
    <script>
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
    </script>
</body>

</html>