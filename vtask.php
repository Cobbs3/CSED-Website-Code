<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <title>Hypertude</title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="dash.css" rel="stylesheet">
</head>

<body class="text-center">

<div class="cover-container d-flex h-100 p-3 mx-auto flex-column">

    <header class="masthead mb-auto">
        <div class="inner">
            <img class="logo" src="logo.png">
            <h3 class="masthead-brand">Hypertude</h3>
            <nav class="nav nav-masthead justify-content-center">
                <div class="btn-toolbar">
                    <form action="dash.php">
                        <button type="submit" class="btn btn-secondary">Back
                    </form>
                </div>

            </nav>
        </div>
    </header>

    <p class="lead">
        <?php
        session_start();

        $userin = $_SESSION['email'];

        $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $db = "main";
        $port = 3306;

        // Create connection
        $conn = new mysqli($servername, $username, $dbpassword, $db, $port);

        $sql = "SELECT * FROM tasks WHERE Email = '$userin' ORDER BY Priority DESC";

        $result = $conn->query($sql);

        $i = 0;

        //If the database contains data check the data
        if ($result->num_rows > 0) {
            $found = false;
            //Check each row
            while ($row = $result->fetch_assoc()) {
                $rows[$i] = $row;
                $i++;
            }
        }

        if($i > 0){
            $mi = $i;
            $i=0;
            for($i=0; $i < $mi; $i++){

                $row = $rows[$i];

                $id = $row['ID'];
                $taskName = $row['TaskName'];
                $startDate = $row['StartDate'];
                $endDate = $row['EndDate'];
                $duration = $row['TaskTime'];
                $percent = $row['Percent'];
                $priority = $row['Priority'];
                $Method = $row['Method'];
                $complete = $row['Completed'];

                $val = "";

                $sql2 = "SELECT * FROM times WHERE Email = '$userin' AND Task = '$taskName' AND ActivityType = 1";

                $time = 0.0;
                $maxTime = floatval($duration);
                if($maxTime == 0) {
                    $maxTime = floatval(strtotime($endDate) - strtotime($startDate)) / 60.0;
                }

                if($duration == 0){
                    $duration = "".$percent."%";
                }

                $result2 = $conn->query($sql2);
                if ($result2->num_rows > 0) {
                    $found = false;
                    //Check each row
                    while ($row = $result2->fetch_assoc()) {
                        $time += floatval($row['Duration']);
                    }
                }

                $sql2 = "SELECT * FROM data WHERE Email = '$userin' AND Task = '$taskName' AND ActivityType = 1";

                $result2 = $conn->query($sql2);
                if ($result2->num_rows > 0) {
                    $found = false;
                    //Check each row
                    while ($row = $result2->fetch_assoc()) {
                        $time += floatval($row['Duration']);
                    }
                }

                $taskProg = min(100.0,((100.0*$time) / $maxTime));

                if($taskProg == 100){
                    $complete = 1;
                }else {
                    $taskProg = number_format((float)$taskProg, 1, '.', '');
                }

                if($complete == 1){
                    $val = "checked";
                }

                echo "<h5><b>Task: </b>" . $taskName . "</h5>";

                echo "<div class=\"modal fade\" id=\"$i\" role=\"dialog\">
                        <div class=\"modal-dialog\">
                    
                            <div class=\"modal-content\">
                                <div class=\"modal-header\">
                                    <h5 class=\"modal-title\">Edit Task</h5>
                                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                    
                                </div>
                                <div class=\"modal-body\">
                    
                                    <form role=\"form\" action=\"edittask.php\" method=\"post\">
                                        <div class=\"form-group\">
                                            <input readonly type=\"text\" name=\"user\" class=\"form-control\" value=\"$userin\">
                                        </div>
                                        <div class=\"form-group\">
                                            <h5>Task</h5>
                                            <input type=\"text\" name=\"task_name\" class=\"form-control\" placeholder=\"Task Name\" value=$taskName>
                                        </div>
                                        <div class=\"form-group\">
                                            <h5>Start Date</h5>
                                            <input id=\"date\" type=\"date\" name=\"start_date\" class=\"form-control\" placeholder=\"Date\" value=$startDate>
                                        </div>
                                        <div class=\"form-group\">
                                            <h5>End Date</h5>
                                            <input id=\"date\" type=\"date\" name=\"end_date\" class=\"form-control\" placeholder=\"Date\" value=$endDate>
                                        </div>
                                        <div class=\"form-group\">
                                            <h5>Work Time</h5>
                                            <input type=\"text\" name=\"work_time\" class=\"form-control\" placeholder=\"Work Time\" value=$duration>
                                        </div>
                                        <div class=\"form-group\">
                                            <h5>Numerical Priority</h5>
                                            <input type=\"text\" name=\"priority\" class=\"form-control\" placeholder=\"Numerical Priority\" value=$priority>
                                        </div>
                                        <div class=\"form-group\">
                                            <h5>Method</h5>
                                            <textarea rows=\"4\" cols=\"50\" name=\"method\" class=\"form-control\" placeholder=\"Method\">$Method</textarea>
                                        </div>
                                        <div class=\"form-group\">
                                            <h5>Complete</h5>
                                            <input type=\"checkbox\" name=\"complete\" class=\"form-control\" $val>
                                        </div>
                                         <div class=\"progress\" style='height: 24px; background-color: #1b1e21'>
                                          <div class=\"progress-bar progress-bar-striped progress-bar-animated\" role=\"progressbar\"
                                            aria-valuenow=$taskProg aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:$taskProg%; color: #ffff00; font-size: small; font-weight: bolder\"> $taskProg%
                                          </div>
                                        </div> 
                                        <br>
                                        <button type=\"submit\" name=\"submit\" class=\"btn btn-primary\" value=$id>Edit Task</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>";

                echo "<div class=\"modal fade\" id=\"delete$i\" role=\"dialog\">
                        <div class=\"modal-dialog\">
                    
                            <div class=\"modal-content\">
                                <div class=\"modal-header\">
                                    <h5 class=\"modal-title\">Delete Task</h5>
                                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                    
                                </div>
                                <div class=\"modal-body\">
                    
                                    <form role=\"form\" action=\"removetask.php\" method=\"post\">
                                        <button type=\"submit\" name=\"submit\" class=\"btn btn-primary\" value=$id>Delete Task</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>";

                echo "<main role=\"main\" class=\"inner cover\"><button type=\"submit\" name=\"report\" class=\"btn btn-success\" data-toggle=\"modal\" data-target=\"#$i\">
                        View Task
                    </button> <button type=\"submit\" name=\"report\" class=\"btn btn-secondary\" data-toggle=\"modal\" data-target=\"#delete$i\">
                        Delete Task
                </button ></main>";
            }
        }else{
            echo "<h2><b>No Tasks Set</b></h2>";
        }

        ?>
        <br/></p>

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

</div>

</body>

</html>
</html>