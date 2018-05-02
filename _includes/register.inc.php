<?php

if(isset($_post['register']){
  include_once 'dbh-inc.php';
  $first = mysqli_real_escape_string($conn, $_post['first']);
  $last = mysqli_real_escape_string($conn, $_post['last']);
  $email = mysqli_real_escape_string($conn, $_post['email']);
  $password = mysqli_real_escape_string($conn, $_post['password']);

  //Error handlers
  if(empty($first) || empty($last) || empty($email) || empty($password)){
    header("Location: /index.php?index=empty");
    exit();
  }else{
    if(!preg_match("/^[a-zA-Z]*$/", $first) || !preg_match("/^[a-zA-Z]*$/", $last) || !preg_match("/^[a-zA-Z]*$", $email) || !preg_match("/^[a-zA-Z]*$", $password)){
      header("Location: /index.php?index=invalid");
      exit();
    }else{
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("Location: /index.php?index=email");
        exit();
      }else{
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (user_first, user_last, user_email, user_password) VALUES ('$first', '$last', '$email', '$hashedPassword');";
        mysqli_query($conn, $sql);
        header("Location: /index.php?index=success");
        exit();
      }
    }
  }

}else{
  header("Location: /index.php");
  exit();
}
