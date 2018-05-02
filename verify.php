<?php

if ($_GET) {
    if (!(isset($_GET['email']))) {
        header("Location: ../home/index.php?index=16");
        exit();
    }
} else {
    header("Location: ../home/index.php?index=16");
    exit();
}

$email = $_GET['email'];

$servername = "localhost";
$username = "root";
$dbpassword = "";
$db = "main";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $dbpassword, $db, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Get the data from the users table
$sql = "UPDATE `users` SET `Activated`='1' WHERE `Email`='" . $email . "'";
$result = $conn->query($sql);

header("Location: ../home/index.php");
exit();