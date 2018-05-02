<?php

if (isset($_POST['submit'])) {

    $conn = mysqli_connect("localhost", "root", "", "main", 3306);

    $id = $_POST["submit"];

    $sql = "SELECT TaskName, Email FROM `tasks` WHERE ID='$id'";

    $taskname = "";
    $email = "";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $taskname = $row["TaskName"];
            $email = $row["Email"];
            $sql = "DELETE FROM `tasks` WHERE ID='$id'";
            mysqli_query($conn, $sql);
            $sql = "DELETE FROM `times` WHERE Task = '$taskname' AND Email = '$email'";
            mysqli_query($conn, $sql);
        }
    }

    header("Location: ../home/vtask.php");
    exit();
}else{
    header("Location: ../home/vtask.php");
    exit();
}
