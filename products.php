<?php
session_start();
include 'db_cnx.php'; // Include your database connection file

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Define the number of products per page
$productsPerPage = 10;

// Fetch all unique categories for the category filter
$sqlCategories = "SELECT DISTINCT Category FROM Products";
$resultCategories = mysqli_query($conn, $sqlCategories);

// Check for errors
if (!$resultCategories) {
    echo "Error: " . mysqli_error($conn);
    exit();
}

// Filter products based on form submissions
if (isset($_POST['filterCategory'])) {
    $filterCategory = $_POST['filterCategory'];
} elseif (isset($_SESSION['filterCategory'])) {
    $filterCategory = $_SESSION['filterCategory'];
} else {
    $filterCategory = '';
}

if (isset($_POST['filterMinPrice'])) {
    $filterMinPrice = $_POST['filterMinPrice'];
} elseif (isset($_SESSION['filterMinPrice'])) {
    $filterMinPrice = $_SESSION['filterMinPrice'];
} else {
    $filterMinPrice = '';
}

if (isset($_POST['filterMaxPrice'])) {
    $filterMaxPrice = $_POST['filterMaxPrice'];
} elseif (isset($_SESSION['filterMaxPrice'])) {
    $filterMaxPrice = $_SESSION['filterMaxPrice'];
} else {
    $filterMaxPrice = '';
}

if (isset($_POST['filterLowStock'])) {
    $filterLowStock = true;
} else {
    $filterLowStock = false;
}

// Store filter values in session variables
$_SESSION['filterCategory'] = $filterCategory;
$_SESSION['filterMinPrice'] = $filterMinPrice;
$_SESSION['filterMaxPrice'] = $filterMaxPrice;

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
if ($filterCategory != '') {
    $sqlCount .= " AND Category = '$filterCategory'";
}

if ($filterMinPrice != '') {
    $sqlCount .= " AND FinalPrice >= '$filterMinPrice'";
}

if ($filterMaxPrice != '') {
    $sqlCount .= " AND FinalPrice <= '$filterMaxPrice'";
}

if ($filterLowStock) {
    $sqlCount .= " AND StockQuantity <= MinQuantity"; // Adjust the threshold as needed
}
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- fontawesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha256-hk1J8HZqEW/p7zC0xjYYr4EhGtYszmJdz21pKBC7ROU=" crossorigin="anonymous" />
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
                    <?php
                    // Check if the user is logged in
                    if (isset($_SESSION['user'])) {
                        // Display welcome message or username
                        echo '<li class="nav-item">';
                        echo '<span class="navbar-text mx-2">Welcome, ' . $_SESSION['user']['Username'] . '!</span>';
                        echo '</li>';

                        // Check if the user is an admin and display the "Dashboard" link
                        $isAdmin = isset($_SESSION["user"]["role"]) ? $_SESSION["user"]["role"] : '';
                        if ($isAdmin === 'admin') {
                            echo '<li class="nav-item">';
                            echo '<a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>';
                            echo '</li>';
                        }

                        // Display the "Logout" link
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
                        echo '</li>';
                    }
                    ?>
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
                        <option value="" <?php echo ($filterCategory == '') ? 'selected' : ''; ?>>All Categories
                        </option>
                        <?php
                        while ($rowCategory = mysqli_fetch_assoc($resultCategories)) {
                            $selected = ($filterCategory == $rowCategory['Category']) ? 'selected' : '';
                            echo "<option value='{$rowCategory['Category']}' $selected>{$rowCategory['Category']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterMinPrice" class="form-label">Min Price:</label>
                    <input type="number" id="filterMinPrice" name="filterMinPrice" class="form-control" step="0.01" value="<?php echo $filterMinPrice; ?>">
                </div>
                <div class="col-md-3">
                    <label for="filterMaxPrice" class="form-label">Max Price:</label>
                    <input type="number" id="filterMaxPrice" name="filterMaxPrice" class="form-control" step="0.01" value="<?php echo $filterMaxPrice; ?>">
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="filterLowStock" name="filterLowStock" <?php echo ($filterLowStock) ? 'checked' : ''; ?>>
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
        <div class="mt-4">
            <p>Total Products: <?php echo $totalProducts; ?></p>
            <p>Products per Page: <?php echo $productsPerPage; ?></p>
            <p>Total Pages: <?php echo $totalPages; ?></p>
            <p>Current Page: <?php echo $page; ?></p>
            <p>Offset: <?php echo $offset; ?></p>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha384-GLhlTQ8iS6LHs pierced YWR1u7kDToSf5NV9In1EJ+sKtwEVR5EJFdm2i5EG98vUuwjA" crossorigin="anonymous"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>