<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

</head>

<body>

    <div class="container mt-5">

        <h2 class="mb-4">Menu</h2>

        <!-- Navigation Menu -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="?page=user-management">User Section</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=category-management">Category Section</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=product-management">Product Section</a>
                    </li>
                </ul>
            </div>
            <a class="nav-link" href="products.php">Return</a>
        </nav>

        <!-- Your CRUD content goes here -->

    </div>
    <?php
    // Check if a page parameter is set in the URL
    if (isset($_GET['page'])) {
        $page = $_GET['page'];

        // Validate and include the selected page
        if (in_array($page, ['user-management', 'category-management', 'product-management']) && file_exists($page . '.php')) {
            include($page . '.php');
        } else {
            echo '<p class="alert alert-danger">Invalid page selected.</p>';
        }
    } else {
        // Default page to include when no specific page is selected
        include('user-management.php');
    }
    ?>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>