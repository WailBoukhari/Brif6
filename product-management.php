<?php
// Check if a request to toggle status is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['toggleStatus'])) {
        $productId = $_POST['productId'];
        $currentStatus = $_POST['currentStatus'];

        // Toggle the status in the database
        $newStatus = $currentStatus ? 0 : 1;
        $sql = "UPDATE Products SET Hidden = $newStatus WHERE ProductID = $productId";
        if ($conn->query($sql) === TRUE) {
            // Handle success
            echo "Status toggled successfully";
            // Redirect to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            // Handle error
            echo "Error toggling status: " . $conn->error;
        }
    }
}

// Check if a request to update a product is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['updateProductForm'])) {
        $productId = $_POST['productId'];
        $updateReference = $_POST['updateReference'];
        $updateLabel = $_POST['updateLabel'];
        $updateBarcode = $_POST['updateBarcode'];
        $updatePurchasePrice = $_POST['updatePurchasePrice'];
        $updateFinalPrice = $_POST['updateFinalPrice'];
        $updatePriceOffer = $_POST['updatePriceOffer'];
        $updateDescription = $_POST['updateDescription'];
        $updateMinQuantity = $_POST['updateMinQuantity'];
        $updateStockQuantity = $_POST['updateStockQuantity'];
        $updateCategory = $_POST['updateCategory'];
        // Handle image upload for update
        if (isset($_FILES["updateImage"]) && $_FILES["updateImage"]["error"] == UPLOAD_ERR_OK) {
            $targetDirectory = "C:\xampp\htdocs\brif6\img";
            $uniqueIdentifier = time();
            $targetFile = $targetDirectory . $uniqueIdentifier . '_' . basename($_FILES["updateImage"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if the image file is a actual image or fake image
            $check = getimagesize($_FILES["updateImage"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            // Check if file already exists
            if (file_exists($targetFile)) {
                echo "Sorry, file already exists.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
            } else {
                if (move_uploaded_file($_FILES["updateImage"]["tmp_name"], $targetFile)) {
                    echo "The file " . htmlspecialchars(basename($_FILES["updateImage"]["name"])) . " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }

        // Perform database update here
        $sql = "UPDATE Products SET Reference='$updateReference', Label='$updateLabel',Barcode='$updateBarcode',
        PurchasePrice='$updatePurchasePrice',FinalPrice='$updateFinalPrice',PriceOffer='$updatePriceOffer',
        Description='$updateDescription',MinQuantity='$updateMinQuantity',StockQuantity='$updateStockQuantity',
        Category='$updateCategory', Image='img/" . basename($_FILES["updateImage"]["name"]) . "' WHERE ProductID=$productId";

        if ($conn->query($sql) === TRUE) {
            // Handle success
            echo "Product updated successfully";
            // Redirect to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            // Handle error
            echo "Error updating product: " . $conn->error;
        }
    }
}

// Check if a request to add a new product is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addProductForm'])) {
        $addReference = $_POST['addReference'];
        $addLabel = $_POST['addLabel'];
        $addBarcode = $_POST['addBarcode'];
        $addPurchasePrice = $_POST['addPurchasePrice'];
        $addFinalPrice = $_POST['addFinalPrice'];
        $addPriceOffer = $_POST['addPriceOffer'];
        $addDescription = $_POST['addDescription'];
        $addMinQuantity = $_POST['addMinQuantity'];
        $addStockQuantity = $_POST['addStockQuantity'];
        $addCategory = $_POST['addCategory'];

        // Handle image upload for add product
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
            $targetDirectory = "C:\xampp\htdocs\brif6\img";
            $uniqueIdentifier = time();
            $targetFile = $targetDirectory . $uniqueIdentifier . '_' . basename($_FILES["image"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if the image file is a actual image or fake image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            // Check if file already exists
            if (file_exists($targetFile)) {
                echo "Sorry, file already exists.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
            } else {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    echo "The file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file. Error: " . $_FILES["image"]["error"];
                }
            }
        }

        // Perform database insertion for a new product
        $sql = "INSERT INTO Products (Reference, Label, Barcode, PurchasePrice, FinalPrice, PriceOffer, Description, MinQuantity, StockQuantity, Category, Image) VALUES ('$addReference', '$addLabel','$addBarcode','$addPurchasePrice','$addFinalPrice','$addPriceOffer','$addDescription','$addStockQuantity','$addDescription','$addCategory', 'img/" . basename($_FILES["image"]["name"]) . "')";

        if ($conn->query($sql) === TRUE) {
            // Handle success
            echo "Product added successfully";
            // Redirect to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            // Handle error
            echo "Error adding product: " . $conn->error;
        }
    }
}

// Fetch products from the database
$sql = "SELECT * FROM Products";
$result = $conn->query($sql);

$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
// Fetch products from the database
$sql1 = "SELECT * FROM Categories";
$result1 = $conn->query($sql1);

$categorys = [];

if ($result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        $categorys[] = $row;
    }
}
$conn->close();
?>
<!-- Your HTML part -->
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
        <h2 class="mb-4">Products</h2>

        <!-- Create button remains the same -->

        <!-- Read -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Imgae</th>
                    <th>Reference</th>
                    <th>Label</th>
                    <th>Barcode</th>
                    <th>Purchase Price</th>
                    <th>Final Price</th>
                    <th>Price Offer</th>
                    <th>Description</th>
                    <th>MinQuantity</th>
                    <th>StockQuantity</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <!-- Loop through products and display them -->
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td><?php echo $product['ProductID']; ?></td>
                        <td><img class="w-75" src="<?php echo $product['Image']; ?>" alt=""></td>
                        <td><?php echo $product['Reference']; ?></td>
                        <td><?php echo $product['Label']; ?></td>
                        <td><?php echo $product['Barcode']; ?></td>
                        <td><?php echo $product['PurchasePrice']; ?></td>
                        <td><?php echo $product['FinalPrice']; ?></td>
                        <td><?php echo $product['PriceOffer']; ?></td>
                        <td><?php echo $product['Description']; ?></td>
                        <td><?php echo $product['MinQuantity']; ?></td>
                        <td><?php echo $product['StockQuantity']; ?></td>
                        <td><?php echo $product['Category']; ?></td>
                        <td><?php echo $product['Hidden']; ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="productId" value="<?php echo $product['ProductID']; ?>">
                                <input type="hidden" name="currentStatus" value="<?php echo $product['Hidden']; ?>">
                                <button type="submit" name="toggleStatus" class="btn btn-<?php echo $product['Hidden'] ? 'danger' : 'success'; ?> btn-sm">
                                    <?php echo $product['Hidden'] ? 'Hide' : 'Unhide'; ?>
                                </button>
                            </form>

                            <button type="button" class="btn btn-primary btn" onclick="showUpdateModal(<?php echo $product['ProductID']; ?>)">
                                Edit
                            </button>
                        </td>
                    </tr>
                    <!-- Update Modal for each users -->
                    <div id="updateModal<?php echo $product['ProductID']; ?>" class="modal" style="display: none;">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel<?php echo $product['ProductID']; ?>">Update
                                        Product</h5>
                                </div>
                                <div class="modal-body">
                                    <!-- Add your form fields for updating a product -->
                                    <form method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="productId" value="<?php echo $product['ProductID']; ?>">
                                        <input type="hidden" name="currentStatus" value="<?php echo $product['Hidden']; ?>">
                                        <div class="mb-3">
                                            <label for="updateReference<?php echo $product['ProductID']; ?>" class="form-label">Reference:</label>
                                            <input type="text" class="form-control" id="updateReference<?php echo $product['ProductID']; ?>" name="updateReference" value="<?php echo $product['Reference']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updateLabel<?php echo $product['ProductID']; ?>" class="form-label">Label:</label>
                                            <input type="text" class="form-control" id="updateLabel<?php echo $product['ProductID']; ?>" name="updateLabel" value="<?php echo $product['Label']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updateBarcode<?php echo $product['ProductID']; ?>" class="form-label">Barcode:</label>
                                            <input type="text" class="form-control" id="updateBarcode<?php echo $product['ProductID']; ?>" name="updateBarcode" value="<?php echo $product['Barcode']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updatePurchasePrice<?php echo $product['ProductID']; ?>" class="form-label">Purchase Price:</label>
                                            <input type="text" class="form-control" id="updatePurchasePrice<?php echo $product['ProductID']; ?>" name="updatePurchasePrice" value="<?php echo $product['PurchasePrice']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updatePurchasePrice<?php echo $product['ProductID']; ?>" class="form-label">Final Price:</label>
                                            <input type="text" class="form-control" id="updateFinalPrice<?php echo $product['ProductID']; ?>" name="updateFinalPrice" value="<?php echo $product['FinalPrice']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updatePriceOffer<?php echo $product['ProductID']; ?>" class="form-label">Price Offer:</label>
                                            <input type="text" class="form-control" id="updatePriceOffer<?php echo $product['ProductID']; ?>" name="updatePriceOffer" value="<?php echo $product['PriceOffer']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updateDescription<?php echo $product['ProductID']; ?>" class="form-label">Description:</label>
                                            <input type="text" class="form-control" id="updateDescription<?php echo $product['ProductID']; ?>" name="updateDescription" value="<?php echo $product['Description']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updateMinQuantity<?php echo $product['ProductID']; ?>" class="form-label">MinQuantity:</label>
                                            <input type="text" class="form-control" id="updateMinQuantity<?php echo $product['ProductID']; ?>" name="updateMinQuantity" value="<?php echo $product['MinQuantity']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updateStockQuantity<?php echo $product['ProductID']; ?>" class="form-label">StockQuantity:</label>
                                            <input type="text" class="form-control" id="updateStockQuantity<?php echo $product['ProductID']; ?>" name="updateStockQuantity" value="<?php echo $product['StockQuantity']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updateCategory<?php echo $product['ProductID']; ?>" class="form-label">Category:</label>
                                            <select class="form-select" id="updateCategory<?php echo $product['ProductID']; ?>" name="updateCategory" required>
                                                <option value="Electronics" <?php echo ($product['Category'] === 'Electronics') ? 'selected' : ''; ?>>
                                                    Electronics</option>
                                                <option value="Robotics" <?php echo ($product['Category'] === 'Robotics') ? 'selected' : ''; ?>>
                                                    Robotics</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="updateImage<?php echo $product['ProductID']; ?>">Update
                                                Image:</label>
                                            <input type="file" name="updateImage" id="updateImage<?php echo $product['ProductID']; ?>" accept=" image/*">
                                        </div>
                                        <button type="submit" name="updateProductForm" class="btn btn-primary">Update
                                            Product</button>
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
        <!-- Add Modal for adding a new users -->
        <div id="addModal" class="modal" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add Product</h5>
                    </div>
                    <div class="modal-body">
                        <!-- Add your form fields for adding a new users -->
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="addReference" class="form-label">Reference:</label>
                                <input type="text" class="form-control" id="addReference" name="addReference" required>
                            </div>
                            <div class="mb-3">
                                <label for="addLabel<?php echo $product['ProductID']; ?>" class="form-label">Label:</label>
                                <input type="text" class="form-control" id="addLabel<?php echo $product['ProductID']; ?>" name="addLabel" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="addBarcode<?php echo $product['ProductID']; ?>" class="form-label">Barcode:</label>
                                <input type="text" class="form-control" id="addBarcode<?php echo $product['ProductID']; ?>" name="addBarcode" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="addPurchasePrice<?php echo $product['ProductID']; ?>" class="form-label">Purchase Price:</label>
                                <input type="text" class="form-control" id="addPurchasePrice<?php echo $product['ProductID']; ?>" name="addPurchasePrice" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="addFinalPrice<?php echo $product['ProductID']; ?>" class="form-label">Final
                                    Price:</label>
                                <input type="text" class="form-control" id="addFinalPrice<?php echo $product['ProductID']; ?>" name="addFinalPrice" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="addPriceOffer<?php echo $product['ProductID']; ?>" class="form-label">Price
                                    Offer:</label>
                                <input type="text" class="form-control" id="addPriceOffer<?php echo $product['ProductID']; ?>" name="addPriceOffer" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="addDescription<?php echo $product['ProductID']; ?>" class="form-label">Description:</label>
                                <input type="text" class="form-control" id="addDescription<?php echo $product['ProductID']; ?>" name="addDescription" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="addMinQuantity<?php echo $product['ProductID']; ?>" class="form-label">MinQuantity:</label>
                                <input type="text" class="form-control" id="addMinQuantity<?php echo $product['ProductID']; ?>" name="addMinQuantity" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="addStockQuantity<?php echo $product['ProductID']; ?>" class="form-label">StockQuantity:</label>
                                <input type="text" class="form-control" id="addStockQuantity<?php echo $product['ProductID']; ?>" name="addStockQuantity" value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="addCategory" class="form-label">Category:</label>
                                <select class="form-select" id="addCategory" name="addCategory" required>
                                    <?php foreach ($categorys as $category) : ?>
                                        <option value="<?php echo $category['CategoryName']; ?>">
                                            <?php echo $category['CategoryName']; ?></option>
                                    <?php endforeach; ?>

                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="image">Image:</label>
                                <input type="file" name="image" id="image" accept="image/*" required>
                            </div>
                            <button type="submit" name="addProductForm" class="btn btn-success">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha384-GLhlTQ8iS6LHs pierced YWR1u7kDToSf5NV9In1EJ+sKtwEVR5EJFdm2i5EG98vUuwjA" crossorigin="anonymous"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <!-- JavaScript for Modals -->
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
    </script>

</body>

</html>