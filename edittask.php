<?php

if (isset($_POST['submit'])) {

    $conn = mysqli_connect("localhost", "root", "", "main", 3306);
    $percent = 0;
    if (!isset($_POST['user']) || $_POST['user']=="") {
        //The error that never happens
        header("Location: ../home/dash.php?index=1");
        exit();
    }
    if (!isset($_POST['task_name']) || $_POST['task_name']=="") {
        //No date was put
        header("Location: ../home/dash.php?index=6");
        exit();
    }
    if (!isset($_POST['start_date']) || $_POST['start_date']=="") {
        //No app was put
        header("Location: ../home/dash.php?index=7");
        exit();
    }
    if (!isset($_POST['end_date']) || $_POST['end_date']=="") {
        //No type selected
        header("Location: ../home/dash.php?index=8");
        exit();
    }
    if (!isset($_POST['work_time']) || $_POST['work_time']=="") {
        //No duration
        header("Location: ../home/dash.php?index=9");
        exit();
    }else{
        $substrpt1 = substr($_POST['work_time'],0,strlen($_POST['work_time'])-1);
        $substrpt2 = substr($_POST['work_time'],strlen($_POST['work_time'])-1,strlen($_POST['work_time']));
        if(is_numeric($_POST['work_time'])){

        }else if(is_numeric($substrpt1)){
            if($substrpt2 == "%"){
                $percent = intval($substrpt1);
            }
        }else{
            //Duration must be a double
            header("Location: ../home/dash.php?index=9");
            exit();
        }
    }
    if(!isset($_POST['priority']) || $_POST['priority'] ==""){
        header("Location: ../home/dash.php?index=10");
        exit();
    }
    if(!isset($_POST['method']) || $_POST['method'] ==""){
        header("Location: ../home/dash.php?index=11");
        exit();
    }

    if(strtotime($_POST['start_date']) >= strtotime($_POST['end_date'])){
        header("Location: ../home/dash.php?index=8");
        exit();
    }

    $id = $_POST["submit"];
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $taskName = mysqli_real_escape_string($conn, $_POST['task_name']);
    $startDate = mysqli_real_escape_string($conn, $_POST['start_date']);
    $endDate = mysqli_real_escape_string($conn, $_POST['end_date']);
    $duration = mysqli_real_escape_string($conn,0);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $completed = mysqli_real_escape_string($conn, 0);

    if(isset($_POST["complete"])){
        $completed = mysqli_real_escape_string($conn, 1);
    }

    if($percent == 0) {
        $duration = mysqli_real_escape_string($conn, $_POST['work_time']);
    }else{
        $percent = mysqli_real_escape_string($conn,$percent);
    }
    $sql = "UPDATE `tasks` SET `TaskName`='$taskName', `StartDate`='$startDate', `EndDate`='$endDate', `TaskTime`='$duration', `Priority`='$priority', `Method`='$method', `Percent`='$percent', `Completed`='$completed' WHERE `ID`='" . $id . "'";
    mysqli_query($conn, $sql);
    header("Location: ../home/vtask.php");
    exit();
}else{
    header("Location: ../home/vtask.php");
    exit();
}
