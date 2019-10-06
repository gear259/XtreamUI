<?php
include "functions.php";
if (isset($_SESSION['user_id'])) { header("Location: ./dashboard.php"); exit; }

if ((isset($_POST["username"])) && (isset($_POST["password"]))) {
    if (doLogin($_POST["username"], $_POST["password"])) {
        header("Location: ./dashboard.php");
    } else {
        $_ERROR = 1;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Xtream Codes - Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- App css -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/app.css" rel="stylesheet" type="text/css" />
    </head>
    <body class="authentication-bg authentication-bg-pattern">
        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <?php if (isset($_ERROR)) { ?>
                        <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Incorrect username or password! Please try again.
                        </div>
                        <?php } else if (checkUpdate()) { ?>
                        <a href="http://xtreamcodes.org">
                            <div class="alert alert-primary alert-dismissible bg-primary text-white border-0 fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                A new update is available! Check it out now!.
                            </div>
                        </a>
                        <?php } ?>
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="text-center w-75 m-auto">
                                    <span><img src="assets/images/logo-back.png" width="250px" alt=""></span>
                                    <p class="text-muted mb-4 mt-3"></p>
                                </div>
                                <h5 class="auth-title">Admin Interface</h5>
                                <form action="./login.php" method="POST">
                                    <div class="form-group mb-3">
                                        <label for="username">Username</label>
                                        <input class="form-control" type="text" id="username" name="username" required="" placeholder="Enter your username">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password">Password</label>
                                        <input class="form-control" type="password" required="" id="password" name="password" placeholder="Enter your password">
                                    </div>
                                    <div class="form-group mb-0 text-center">
                                        <button class="btn btn-danger btn-block" type="submit"> SIGN IN </button>
                                    </div>
                                </form>
                            </div> <!-- end card-body -->
                        </div>
                        <!-- end card -->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->
        <footer class="footer footer-alt">Xtream Codes - Admin UI</footer>
        <!-- Vendor js -->
        <script src="assets/js/vendor.min.js"></script>
        <!-- App js -->
        <script src="assets/js/app.min.js"></script>
    </body>
</html>