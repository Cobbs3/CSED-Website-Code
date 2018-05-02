<?php

$message = "";
$attempt1 = "Log In ";
$attempt2 = "Failed";

//Was an index passed? If so do stuff
if (isset($_GET["index"])) {
    $error = $_GET["index"];

    //Establish title for given error box
    if ($error >= 10 && $error < 20) {
        $attempt1 = "Sign Up ";
    } else if ($error >= 20) {
        $attempt2 = "Successful";
    }

    //Check the error code that was passed in and set the message accordingly
    switch ($error) {
        case 0:
            $message = "User not Found";
            break;
        case 1:
            $message = "Incorrect Password";
            break;
        case 2:
            $message = "Form Timeout";
            break;
        case 3:
            $message = "Invalid Email";
            break;
        case 4:
            $message = "Email not yet verified";
            break;
        case 5:
            $message = "Database Currently Down";
            break;
        case 10:
            $message = "Email Taken";
            break;
        case 11:
            $message = "Invalid Email";
            break;
        case 12:
            $message = "Database Error";
            break;
        case 13:
            $message = "Invalid Password";
            break;
        case 14:
            $message = "Passwords do not match";
            break;
        case 15:
            $message = "Required field not filled!";
            break;
        case 16:
            $message = "Unable to verify email";
            break;
        case 17:
            $message = "Database Currently Down";
            break;
        case 20:
            $message = "";
            break;
        case 21:
            $attempt1 = "Sign Up ";
            $message = "Account Created";
            break;
    }

}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <title>Hypertude</title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="index.css" rel="stylesheet">
</head>

<body class="text-center">

<div class="cover-container d-flex h-100 p-3 mx-auto flex-column">

    <header class="masthead mb-auto">
        <div class="inner">
            <img class="logo" src="logo.png">
            <h3 class="masthead-brand">Hypertude</h3>
            <nav class="nav nav-masthead justify-content-center">

                <div class="btn-toolbar">
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#registerModal">
                        Register
                    </button>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#signinModal">Sign
                        In
                    </button>
                    <form action="about.php">
                        <button type="submit" class="btn btn-success">About
                    </form>
                </div>

                <!-- Sign in Modal -->
                <div class="modal fade" id="signinModal" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Sign In</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>

                            </div>
                            <div class="modal-body">
                                <p>Sign in to view your account.</p>

                                <form role="form" action="attempt.php" method="post">
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control" placeholder="Email">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control"
                                               placeholder="Password">
                                    </div>
                                    <button type="submit" name="action" class="btn btn-primary" value="Log In">Sign In
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Register Modal -->
                <div class="modal fade" id="registerModal" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Register</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>

                            </div>
                            <div class="modal-body">
                                <p>Register a new account.</p>

                                <form role="form" action="attempt.php" method="post">
                                    <div class="form-group">
                                        <input type="text" name="first" class="form-control" placeholder="First name">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="last" class="form-control" placeholder="Last name">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control" placeholder="Email">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control"
                                               placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="cpassword" class="form-control"
                                               placeholder="Confirm Password">
                                    </div>
                                    <button type="submit" name="action" class="btn btn-primary" value="Sign Up">
                                        Register
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="errorModal" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?=$attempt1.$attempt2?></h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>

                            </div>
                            <div class="modal-body">
                                <h4><?=$message?></h4>
                            </div>
                        </div>
                    </div>
                </div>


            </nav>
        </div>
    </header>


    <h1 class="cover-heading">Manage your time.</h1>
    <p class="lead" color="white">Lorem ipsum dolor sit amet, consectetur adipiscing elit. <br/>
        Excepteur sint occaecat cupidatat non proident, sunt in culpa qui.</p>
    <p class="lead">
    </p>


    <footer class="mastfoot mt-auto">
    </footer>
</div>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
<script src="assets/js/vendor/popper.min.js"></script>
<script src="dist/js/bootstrap.min.js"></script>
</body>
</html>

<!--Open the error display modal if there is an error-->
<?php if($message != ""){ ?>
        <script type="text/javascript"> $('#errorModal').modal('show'); </script>
<?php } ?>

