<?php
include 'db_cnx.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Add validation and security checks here if needed

    // Check if the user already exists
    $checkUserQuery = "SELECT * FROM Users WHERE Email = '$email' OR Username = '$username'";
    $checkUserResult = $conn->query($checkUserQuery);

    if ($checkUserResult->num_rows > 0) {
        echo "User with this email or username already exists. Please use a different email or username.";
    } else {
        // Set Verified to FALSE by default
        $sql = "INSERT INTO Users (Username, Email, Password, Verified) VALUES ('$username', '$email', '$password', FALSE)";
        if ($conn->query($sql) === TRUE) {
            echo "User registered successfully. Please wait for verification.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Register</title>
    <!-- MDB icon -->
    <link rel="icon" href="" type="image/x-icon" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <!-- Google Fonts Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
    <!-- Bootstrap Css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .login {
            min-height: 100vh;
        }

        .bg-image {
            background-image: url('https://source.unsplash.com/WEQbe2jBg40/600x1200');
            background-size: cover;
            background-position: center;
        }

        .login-heading {
            font-weight: 300;
        }

        .btn-login {
            font-size: 0.9rem;
            letter-spacing: 0.05rem;
            padding: 0.75rem 1rem;
        }
    </style>
</head>

<body>
    <!-- Start your project here-->

    <section>
        <div class="container-fluid ps-md-0">
            <div class="row g-0">
                <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
                <div class="col-md-8 col-lg-6">
                    <div class="login d-flex align-items-center py-5">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-9 col-lg-8 mx-auto">
                                    <h3 class="login-heading mb-4">Signup Here</h3>

                                    <!-- Sign In Form -->
                                    <form method="post">
                                        <div class="form-floating mb-3">
                                            <input type="username" class="form-control" id="floatingInput" name="username" placeholder="Username">
                                            <label for="floatingInput">Username</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com">
                                            <label for="floatingInput">Email address</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password">
                                            <label for="floatingPassword">Password</label>
                                        </div>


                                    </form>

                                    <div class="d-grid">
                                        <button class="btn btn-lg btn-primary btn-login text-uppercase fw-bold mb-2" type="submit">Sign in</button>
                                        <div class="text-center">
                                            <p class="small">You already have an account ?<a href="index.php" class=""> login here</a> </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End your project here-->

    <!-- Bootstrap Js-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- Custom scripts -->
    <!-- Your existing HTML and PHP code for registration form -->


</body>

</html>