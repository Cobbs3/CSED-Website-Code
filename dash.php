<?php
session_start();

//Connect to the database
$conn = mysqli_connect("localhost", "root", "", "main", 3306);

$userin = $_SESSION['email'];

$sql = "SELECT * FROM users WHERE Email = '$userin'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$login_user = $row['FirstName'];
$user_email = $row['Email'];

$_SESSION['email'] = $user_email;
$_SESSION['name'] = $login_user;

//Return to main if user account is not found
if (!isset($userin)) {
    header("Location: ../home/index.php?index=0");
}

$message = "";

$entryTaskName = "";
$entryStartDate = "";
$entryEndDate = "";
$entryWorkTime = "";
$entryPriority = "";
$entryMethod = "";

$entryDate = "";
$entryApp = "";
$entryCategory = "Category";
$entryDuration = "";
$entryTask = "Task";

if(isset($_SESSION['entry_task_name']) && $_SESSION['entry_task_name'] != ""){
    $entryTaskName = $_SESSION['entry_task_name'];
}

if(isset($_SESSION['entry_start_date']) && $_SESSION['entry_start_date'] != ""){
    $entryStartDate = $_SESSION['entry_start_date'];
}

if(isset($_SESSION['entry_end_date']) && $_SESSION['entry_end_date'] != ""){
    $entryEndDate = $_SESSION['entry_end_date'];
}

if(isset($_SESSION['entry_work_time']) && $_SESSION['entry_work_time'] != ""){
    $entryWorkTime = $_SESSION['entry_work_time'];
}

if(isset($_SESSION['entry_priority']) && $_SESSION['entry_priority'] != ""){
    $entryPriority = $_SESSION['entry_priority'];
}

if(isset($_SESSION['entry_method']) && $_SESSION['entry_method'] != ""){
    $entryMethod = $_SESSION['entry_method'];
}

if(isset($_SESSION['entry_date']) && $_SESSION['entry_date'] != ""){
    $entryDate = $_SESSION['entry_date'];
}

if(isset($_SESSION['entry_app']) && $_SESSION['entry_app'] != ""){
    $entryApp = $_SESSION['entry_app'];
}

if(isset($_SESSION['entry_category']) && $_SESSION['entry_category'] != ""){
    $entryCategory = $_SESSION['entry_category'];
}

if(isset($_SESSION['entry_duration']) && $_SESSION['entry_duration'] != ""){
    $entryDuration = $_SESSION['entry_duration'];
}

if(isset($_SESSION['entry_task']) && $_SESSION['entry_task'] != ""){
    $entryTask = $_SESSION['entry_task'];
}

$_SESSION['entry_task_name'] = "";
$_SESSION['entry_start_date'] = "";
$_SESSION['entry_end_date'] = "";
$_SESSION['entry_work_time'] = "";
$_SESSION['entry_priority'] = "";
$_SESSION['entry_method'] = "";
$_SESSION['entry_date'] = "";
$_SESSION['entry_app'] = "";
$_SESSION['entry_category'] = "";
$_SESSION['entry_duration'] = "";
$_SESSION['entry_task'] = "";


$sql = "SELECT TaskName FROM tasks WHERE Email = '$userin'";

$result = $conn->query($sql);

$i = 0;

//If the database contains data check the data
if ($result->num_rows > 0) {
    //Check each row
    while ($row = $result->fetch_assoc()) {

        $tasks[$i] = $row['TaskName'];

        $i++;
    }
}

//Check if there is an error code here
if (isset($_GET["index"])) {
    $error = $_GET["index"];

    //Set the error message based on the code returned
    switch ($error) {
        case 1:
            $message = "Invalid User";
            break;
        case 2:
            $message = "Invalid Date";
            break;
        case 3:
            $message = "Invalid Application";
            break;
        case 4:
            $message = "Invalid Category";
            break;
        case 5:
            $message = "Invalid Duration";
            break;
        case 6:
            $message = "Invalid Task Name";
            break;
        case 7:
            $message = "Invalid Start Date";
            break;
        case 8:
            $message = "Invalid End Date";
            break;
        case 9:
            $message = "Invalid Work Time";
            break;
        case 10:
            $message = "Invalid Priority";
            break;
        case 11:
            $message = "Invalid Method";
            break;
        case 12:
            $message = "Invalid Task";
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

    <!-- Custom styles for this template -->
    <link href="dash.css" rel="stylesheet">
</head>

<body class="text-center">

<main class="cover-container d-flex h-100 p-3 mx-auto flex-column">

    <header class="masthead mb-auto">
        <div class="inner">
            <img class="logo" src="logo.png">
            <h3 class="masthead-brand">Hypertude</h3>
            <nav class="nav nav-masthead justify-content-center">

                <div class="btn-toolbar">
                    <form action="help.php">
                        <button type="submit" class="btn btn-secondary">Help</button>
                    </form>
                    <form role="form" action="profile.php" method="post">
                        <button type="submit" name="profile" class="btn btn-primary">My Profile</button>
                    </form>
                    <form role="form" action="logout.php" method="post">
                        <button type="submit" name="logout" class="btn btn-success">Logout</button>
                    </form>
                </div>

            </nav>
        </div>
    </header>


    <main role="main" class="inner cover">
        <h1 class="cover-heading ">Welcome, <?= $login_user ?>.</h1>
        <button type="submit" name="report" class="btn btn-lg btn-success" data-toggle="modal" data-target="#task">
            Set a Task
        </button>
        <form action="vtask.php">
            <button type="submit" class="btn btn-lg btn-success">View Tasks</button>
        </form>
        <button type="submit" name="report" class="btn btn-lg btn-success" data-toggle="modal" data-target="#report">
            Self report
        </button>
        <form action="vdata.php">
            <button type="submit" class="btn btn-lg btn-success">View Data</button>
        </form>


        <!-- Sign in Modal -->
        <div class="modal fade" id="report" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Self report</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>
                    <div class="modal-body">
                        <p>Manage your time.</p>

                        <form role="form" action="selfdata.php" method="post">
                            <div class="form-group">
                                <input readonly type="text" name="user" class="form-control" value="<?= $user_email ?>">
                            </div>
                            <div class="form-group">
                                <input id="date" type="date" name="date" class="form-control" placeholder="Date" value="<?= $entryDate ?>">
                            </div>
                            <div class="form-group">
                                <input list="applications" name="app" class="form-control" placeholder="Application" value="<?= $entryApp ?>">
                                <datalist id="applications">
                                    <option>eBay</option>
                                    <option>Facebook</option>
                                    <option>Netflix</option>
                                    <option>Spotify</option>
                                    <option>Twitter</option>
                                    <option>YouTube</option>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="category" id="cat-form">
                                    <option hidden><?= $entryCategory ?></option>
                                    <option>Work</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" name="duration" class="form-control"
                                       placeholder="Duration (minutes)" value="<?= $entryDuration ?>">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="task" id="cat-form">
                                    <option hidden><?= $entryTask ?></option>
                                    <option>No Task</option>
                                    <?php
                                        for($j = 0; $j < $i; $j++){
                                            echo "<option>$tasks[$j]</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Submit data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Set Task Modal -->
        <div class="modal fade" id="task" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Set a Task</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>
                    <div class="modal-body">

                        <form role="form" action="selftask.php" method="post">
                            <div class="form-group">
                                <input readonly type="text" name="user" class="form-control" value="<?= $user_email ?>">
                            </div>
                            <div class="form-group">
                                <input type="text" name="task_name" class="form-control" placeholder="Task Name" value="<?= $entryTaskName ?>">
                            </div>
                            <div class="form-group">
                                <h5>Start Date</h5>
                                <input id="date" type="date" name="start_date" class="form-control" placeholder="Date" value="<?= $entryStartDate ?>">
                            </div>
                            <div class="form-group">
                                <h5>End Date</h5>
                                <input id="date" type="date" name="end_date" class="form-control" placeholder="Date" value="<?= $entryEndDate ?>">
                            </div>
                            <div class="form-group">
                                <input type="text" name="work_time" class="form-control" placeholder="Work Time" value="<?= $entryWorkTime ?>">
                            </div>
                            <div class="form-group">
                                <input type="text" name="priority" class="form-control" placeholder="Numerical Priority" value="<?= $entryPriority ?>">
                            </div>
                            <div class="form-group">
                                <textarea rows="4" cols="50" name="method" class="form-control" placeholder="Method"><?= $entryMethod ?></textarea>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Set Task</button>
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
                        <h5 class="modal-title">Self Report Failed</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>
                    <div class="modal-body">
                        <h4><?=$message?></h4>
                    </div>
                </div>
            </div>
        </div>

    </main>


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
</main>
</body>
</html>

<!--Open the error display modal if there is an error-->
<?php if($message != ""){ ?>
    <script type="text/javascript"> $('#errorModal').modal('show'); </script>
<?php } ?>
