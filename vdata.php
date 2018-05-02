<!doctype html>
<?php

session_start();

$userin = $_SESSION['email'];

$sql = "SELECT TaskName FROM tasks WHERE Email = '$userin'";

$servername = "localhost";
$username = "root";
$dbpassword = "";
$db = "main";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $dbpassword, $db, $port);

$result = $conn->query($sql);

$k = 0;

//If the database contains data check the data
if ($result->num_rows > 0) {
    //Check each row
    while ($row = $result->fetch_assoc()) {

        $tasks[$k] = $row['TaskName'];

        $k++;
    }
}

$sql = "SELECT Task FROM data WHERE Email = '$userin'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    //Check each row
    while ($row = $result->fetch_assoc()) {

        $works = true;
        for($l = 0; $l < $k; $l++){
            if($tasks[$l] == $row['Task']){
                $works = false;
                break;
            }
        }

        if($works){
            $tasks[$k] = $row['Task'];
            $k++;
        }
    }
}

?>
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
                    <button type="submit" name="report" class="btn btn-primary btn-success" data-toggle="modal" data-target="#task">
                        Select Task
                    </button>
                    <button type="submit" name="report" class="btn btn-success" data-toggle="modal" data-target="#date">
                        Select Dates
                    </button>
                </div>

            </nav>
        </div>
    </header>

    <p class="lead">
        <div id="piechart" class="inner" align="center"></div>
        <?php

        $sort = "";
        $taskSelect = "";
        $strD = "";
        $endD = "";

        if(isset($_POST['sort'])){
            $sort = $_POST['sort'];
            if($sort == "date"){
                $strD = $_POST['start_date'];
                $endD = $_POST['end_date'];
            }else{
                $taskSelect = $_POST['task'];
                if($taskSelect == "Task" || $taskSelect == "No Task"){
                    $taskSelect = "";
                }
            }
        }

        $sql = "SELECT * FROM data WHERE Email = '$userin'";

        if($strD != ""){
            $sql = "SELECT * FROM data WHERE Email = '$userin' AND ActivityDate BETWEEN '$strD' AND '$endD'";
        }else if($taskSelect != ""){
            $sql = "SELECT * FROM data WHERE Email = '$userin' AND Task = '$taskSelect'";
        }

        $result = $conn->query($sql);

        $i = 0;

        $durWork = 0;
        $durOther = 0;

        //If the database contains data check the data

        $wrk = false;

        $wrk = $result->num_rows > 0;

        if ($wrk) {
            $found = false;
            //Check each row
            while ($row = $result->fetch_assoc()) {
                $rows[$i] = $row;
                if ($row['ActivityType'] == 1) {
                    $durWork += $row['Duration'];
                } else {
                    $durOther += $row['Duration'];
                }
                $i++;
            }
        }

        $sql = "SELECT * FROM times WHERE Email = '$userin'";

        if($strD != ""){
            $sql = "SELECT * FROM times WHERE Email = '$userin' AND ActivityDate BETWEEN '$strD' AND '$endD'";
        }else if($taskSelect != ""){
            $sql = "SELECT * FROM times WHERE Email = '$userin' AND Task = '$taskSelect'";
        }

        $result = $conn->query($sql);

        $wrk2 = $result->num_rows > 0;

        if ($wrk2) {
            $found = false;
            //Check each row
            while ($row = $result->fetch_assoc()) {
                $rows[$i] = $row;
                if ($row['ActivityType'] == 1) {
                    $durWork += $row['Duration'];
                } else {
                    $durOther += $row['Duration'];
                }
                $i++;
            }
        }

        if($wrk || $wrk2){

            $percentNone = ($durOther * 100.0) / ($durWork + $durOther);

            if ($percentNone > 50) {
                echo "<h1 style='color: red'>You need to do more work!</h1>";
            } else if ($percentNone < 30) {
                echo "<h1 style='color: green'>You are working well!</h1>";
            }

            ?>

            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

            <script type="text/javascript">
                // Load google charts
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);

                // Draw the chart and set the chart values
                function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                        ['Task', 'Hours per Day'],
                        ['Work', <?=$durWork?>],
                        ['Other', <?=$durOther?>]
                    ]);

                    // Optional; add a title and set the width and height of the chart
                    var options = {
                        width: 360,
                        height: 400,
                        backgroundColor: '#EEEEEE',
                        is3D: true,
                        legend: {position:"top", alignment:"center"}
                    };

                    // Display the chart inside the <div> element with id="piechart"
                    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                    chart.draw(data, options);
                }
            </script>

            <?php

            echo "<table class='table-bordered' align='center' style='font-size: 20px; width: 70%'><tr><th>Activity Name</th><th>Activity Type</th><th>Activity Date</th><th>Duration</th>" . (($taskSelect=="") ? "<th>Task</th>" : "");

            for($j = 0; $j < $i; $j++){
                $row = $rows[$j];
                $txt = "Other";
                if($row['ActivityType'] == 1){
                    $txt = "Work";
                }
                echo "<tr><td>" . $row['ActivityName'] . "</td><td>" . $txt . "</td><td>" . $row['ActivityDate'] . "</td><td>" . $row['Duration'] . " Minutes</td>" . (($taskSelect=="") ? "<td>". $row['Task'] ."</td>" : "");
            }

            echo "</table>";

        }else{
            echo "<h2><b>No Data</b></h2>";
        }

        ?>

        <div class="modal fade" id="task" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Show For Task</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>
                    <div class="modal-body">

                        <form role="form" action="vdata.php" method="post">
                            <div class="form-group">
                                <select class="form-control" name="task" id="cat-form">
                                    <option hidden>Task</option>
                                    <option>No Task</option>
                                    <?php
                                    for($j = 0; $j < $k; $j++){
                                        echo "<option>$tasks[$j]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="sort" value="task" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="date" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Show For Dates</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>
                    <div class="modal-body">

                        <form role="form" action="vdata.php" method="post">
                            <div class="form-group">
                                <h5>Start Date</h5>
                                <input id="date" type="date" name="start_date" class="form-control" placeholder="Date">
                            </div>
                            <div class="form-group">
                                <h5>End Date</h5>
                                <input id="date" type="date" name="end_date" class="form-control" placeholder="Date">
                            </div>
                            <button type="submit" name="sort" value="date" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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
