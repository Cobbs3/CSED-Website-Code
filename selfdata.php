<?php

session_start();

if (isset($_POST['submit'])) {

    $conn = mysqli_connect("localhost", "root", "", "main", 3306);

    $_SESSION['entry_date'] = $_POST['date'];
    $_SESSION['entry_app'] = $_POST['app'];
    $_SESSION['entry_category'] = $_POST['category'];
    $_SESSION['entry_duration'] = $_POST['duration'];
    $_SESSION['entry_task'] = $_POST['task'];

    if (!isset($_POST['user']) || $_POST['user']=="") {
        //The error that never happens
        header("Location: ../home/dash.php?index=1");
        exit();
    }
    if (!isset($_POST['date']) || $_POST['date']=="") {
        //No date was put
        header("Location: ../home/dash.php?index=2");
        exit();
    }
    if (!isset($_POST['app']) || $_POST['app']=="") {
        //No app was put
        header("Location: ../home/dash.php?index=3");
        exit();
    }
    if (!isset($_POST['category']) || $_POST['category']=="Category") {
        //No type selected
        header("Location: ../home/dash.php?index=4");
        exit();
    }
    if (!isset($_POST['duration']) || $_POST['duration']=="") {
        //No duration
        header("Location: ../home/dash.php?index=5");
        exit();
    }else{
        if(!is_numeric($_POST['duration'])){
            //Duration must be a double
            header("Location: ../home/dash.php?index=5");
            exit();
        }
    }
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $app = mysqli_real_escape_string($conn, $_POST['app']);
    $category = mysqli_real_escape_string($conn, $_POST['category'] == "Work" ? 1 : 0);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);

    if (!isset($_POST['task']) || $_POST['task']=="Task" || $_POST['task']=="No Task") {
        $task = mysqli_real_escape_string($conn, null);
    }else{
        $task = mysqli_real_escape_string($conn, $_POST['task']);
    }

    $sql = "INSERT INTO times (Email, ActivityName, ActivityType, ActivityDate, Duration, Task) VALUES ('$user', '$app', '$category', '$date', '$duration', '$task');";
    mysqli_query($conn, $sql);
    $_SESSION['entry_date'] = "";
    $_SESSION['entry_app'] = "";
    $_SESSION['entry_category'] = "";
    $_SESSION['entry_duration'] = "";
    $_SESSION['entry_task'] = "";
    header("Location: ../home/dash.php?index=0");
    exit();
}else{
    header("Location: ../home/dash.php");
    exit();
}
