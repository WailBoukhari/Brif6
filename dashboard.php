<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- fontawesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha256-hk1J8HZqEW/p7zC0xjYYr4EhGtYszmJdz21pKBC7ROU=" crossorigin="anonymous" />

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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha384-GLhlTQ8iS6LHs pierced YWR1u7kDToSf5NV9In1EJ+sKtwEVR5EJFdm2i5EG98vUuwjA" crossorigin="anonymous"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

</body>

</html>