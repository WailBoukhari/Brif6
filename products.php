<?php
include 'db_cnx.php'; // Include your database connection file
session_start();

// Define the number of products per page
$productsPerPage = 9;

// Fetch all unique categories for the category filter
$sqlCategories = "SELECT DISTINCT Category FROM Products";
$resultCategories = mysqli_query($conn, $sqlCategories);

// Check for errors
if (!$resultCategories) {
    echo "Error: " . mysqli_error($conn);
    exit();
}

// Filter products based on form submissions
$filterCategory = isset($_POST['filterCategory']) ? $_POST['filterCategory'] : '';
$filterMinPrice = isset($_POST['filterMinPrice']) ? $_POST['filterMinPrice'] : '';
$filterMaxPrice = isset($_POST['filterMaxPrice']) ? $_POST['filterMaxPrice'] : '';
$filterLowStock = isset($_POST['filterLowStock']) ? true : false;

// Get the current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $productsPerPage;

// Construct the SQL query based on the filters with pagination
$sqlFilter = "SELECT * FROM Products WHERE Hidden = 0";

if ($filterCategory != '') {
    $sqlFilter .= " AND Category = '$filterCategory'";
}

if ($filterMinPrice != '') {
    $sqlFilter .= " AND FinalPrice >= '$filterMinPrice'";
}

if ($filterMaxPrice != '') {
    $sqlFilter .= " AND FinalPrice <= '$filterMaxPrice'";
}

if ($filterLowStock) {
    $sqlFilter .= " AND StockQuantity <= MinQuantity"; // Adjust the threshold as needed
}

$sqlFilter .= " LIMIT $productsPerPage OFFSET $offset";

$resultFilteredProducts = mysqli_query($conn, $sqlFilter);

// Check for errors
if (!$resultFilteredProducts) {
    echo "Error: " . mysqli_error($conn);
    exit();
}

// Count the total number of products for pagination
$sqlCount = "SELECT COUNT(*) AS total FROM Products WHERE Hidden = 0";

$resultCount = mysqli_query($conn, $sqlCount);

if (!$resultCount) {
    echo "Error: " . mysqli_error($conn);
    exit();
}

$rowCount = mysqli_fetch_assoc($resultCount);
$totalProducts = $rowCount['total'];

// Calculate the total number of pages
$totalPages = ceil($totalProducts / $productsPerPage);

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Add your custom CSS styles -->
    <style>
        body {
            padding: 20px;
        }

        .product-card {
            margin-bottom: 20px;
        }

        .star-rating {
            color: #f8d80d;
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top d-flex">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="#">
                ELECTRONACER
            </a>

            <!-- Navbar Toggler for small screens -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <!-- Dashboard Button (Only for Admin) -->
                    <?php
                    $isAdmin = true; // Replace this with your actual logic to determine if the user is an admin
                    if ($isAdmin) {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>';
                        echo '</li>';
                    }
                    ?>
                    <!-- Add more navigation items as needed -->
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Filtered Products</h1>

        <!-- Filter Form -->
        <form method="post" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filterCategory" class="form-label">Category:</label>
                    <select id="filterCategory" name="filterCategory" class="form-select">
                        <option value="" selected>All Categories</option>
                        <?php
                        while ($rowCategory = mysqli_fetch_assoc($resultCategories)) {
                            echo "<option value='{$rowCategory['Category']}'>{$rowCategory['Category']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterMinPrice" class="form-label">Min Price:</label>
                    <input type="number" id="filterMinPrice" name="filterMinPrice" class="form-control" step="0.01">
                </div>
                <div class="col-md-3">
                    <label for="filterMaxPrice" class="form-label">Max Price:</label>
                    <input type="number" id="filterMaxPrice" name="filterMaxPrice" class="form-control" step="0.01">
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="filterLowStock" name="filterLowStock">
                        <label class="form-check-label" for="filterLowStock">Show Low Stock</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label"></label>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </div>
        </form>

        <!-- Filtered Products Cards -->
        <div class="row">
            <?php
            while ($rowProduct = mysqli_fetch_assoc($resultFilteredProducts)) {
                echo '<div class="col-md-4 w-25">';
                echo '<div class="card product-card">';
                echo '<img src="' . $rowProduct['Image'] . '" class="card-img-top w-50 m-auto" alt="Product Image">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $rowProduct['Label'] . '</h5>';

                echo '<p class="card-text">Category: ' . $rowProduct['Description'] . '</p>';
                echo '<p class="card-text">Category: ' . $rowProduct['Category'] . '</p>';
                echo '<p class="card-text">Final Price: $' . number_format($rowProduct['FinalPrice'], 2) . '</p>';
                echo '<p class="card-text">Stock Quantity: ' . $rowProduct['StockQuantity'] . '</p>';
                echo '<div class="star-rating">';
                echo '
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }

            ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <?php
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                    echo '<a class="page-link" href="?page=' . $i . '">' . $i . '</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome JS -->
    <script src="https://kit.fontawesome.com/ea3542be0c.js" crossorigin="anonymous"></script>
</body>

</html>