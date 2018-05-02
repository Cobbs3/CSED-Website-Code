<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

if ($_POST) {
    if (!(isset($_POST['action']) && isset($_POST['email']) && isset($_POST['password']))) {
        header("Location: ../home/index.php?index=2");
        exit();
    }
} else {
    header("Location: ../home/index.php?index=2");
    exit();
}

$action = $_POST["action"];
$email = $_POST["email"];
$password = $_POST["password"];

$signingup = $action == "Sign Up";

//Check if the email given is valid
if (!preg_match("/[^@]+[^.]+$/", $email)) {
    if (!$signingup) {
        header("Location: ../home/index.php?index=3");
        exit();
    } else {
        header("Location: ../home/index.php?index=11");
        exit();
    }
}

if (!$signingup) {
    $value = attemptLogin($email, $password);
    if ($value == 20) {
        $_SESSION['email'] = $email;
        header("Location: ../home/dash.php");
    } else {
        header("Location: ../home/index.php?index=" . $value);
    }
    exit();
} else {
    $firstname = $_POST['first'];
    $surname = $_POST['last'];
    $cpassword = $_POST['cpassword'];

    $value = attemptSignup($email, $password, $firstname, $surname, $cpassword);
    if ($value == 21) {
        header("Location: ../home/index.php?index=" . $value);
    } else {
        header("Location: ../home/index.php?index=" . $value);
    }
    exit();
}

/**
 * @param $email : The user's email
 * @param $password : The user's password
 * @return int: The error code to be handled
 */
function attemptLogin($email, $password) {

    //Connect to the sql server
    $servername = "localhost";
    $username = "root";
    $dbpassword = "";
    $db = "main";
    $port = 3306;

    // Create connection
    $conn = new mysqli($servername, $username, $dbpassword, $db, $port);

    // Check connection
    if ($conn->connect_error) {
        return 5;
    }

    //Get the data from the users table
    $sql = "SELECT ID, Password, Email, Salt, Activated FROM users";
    $result = $conn->query($sql);

    //If the database contains data check the data
    if ($result->num_rows > 0) {
        $found = false;
        $passWrong = false;
        //Check each row
        while ($row = $result->fetch_assoc()) {

            //User with that email exists
            if ($email == $row["Email"]) {
                $found = true;
                $salt = $row['Salt'];
                $hashed_pass = hash("sha256", $password);
                if ($salt != null) {
                    $hashed_pass = hash_pbkdf2("sha256", $password, $salt, 1);
                }
                //Check that the hash of the entered password matches that stored in the database
                if ($hashed_pass == $row["Password"]) {
                    if ($row["Activated"] == 0) {
                        return 4;
                    } else {
                        return 20;
                    }
                } else {
                    $passWrong = true;
                }
                break;
            }

        }

        //Error handling
        if (!$found) {
            return 0;
        } else if ($passWrong) {
            return 1;
        }

    }
}

/**
 * @param $email : The user's email
 * @param $password : The user's password
 * @param $firstname : The user's firstname
 * @param $surname : The user's surname
 * @param $cpassword : The confirmation password entry
 * @return int: The error code to be handled
 */
function attemptSignup($email, $password, $firstname, $surname, $cpassword) {

    trY {

        if ($cpassword != $password) {
            return 14;
        }

        if ($firstname == "" || $surname == "" || $cpassword == "") {
            return 15;
        }

        if ($password == "") {
            return 13;
        }

        $servername = "localhost";
        $username = "root";
        $dbpassword = "";
        $db = "main";
        $port = 3306;

        // Create connection
        $conn = new mysqli($servername, $username, $dbpassword, $db, $port);

        $checkEmail = mysqli_real_escape_string($conn,$email);

        // Check connection
        if ($conn->connect_error) {
            return 17;
        }

        //Get the data from the users table
        $sql = "SELECT ID, Email FROM users WHERE Email='$checkEmail'";
        $result = $conn->query($sql);

        //If the database contains data check the data
        if ($result->num_rows > 0) {
            return 10;
        } else {
            //Empty database handle
            $salt = 12345567890;
            try {
                $salt = random_int((int)10E6, (int)10E8);
            } catch (Exception $e) {
            }
            $hashed_pass = hash_pbkdf2("sha256", $password, $salt, 1);
            return addEntry($conn, $hashed_pass, $email, $firstname, $surname, $salt);
        }

    } catch (\Exception $e) {
        return 15;
    }
}

/**
 * Adds a new entry to the database with the following parameters
 * @param $conn Database object we are connected to
 * @param $hashed_pass The hash of the entered password
 * @param $email The email address entered
 * @param $firstname The person's first name
 * @param $surname The person's surname
 * @param $salt The salt for the password hash
 * @return int The error code to be handled
 */
function addEntry($conn, $hashed_pass, $email, $firstname, $surname, $salt)
{

    $emailVal = $email;
    $firstname = mysqli_real_escape_string($conn, $firstname);
    $surname = mysqli_real_escape_string($conn, $surname);
    $hashed_pass = mysqli_real_escape_string($conn, $hashed_pass);
    $email = mysqli_real_escape_string($conn, $email);
    $salt = mysqli_real_escape_string($conn, $salt);

    //Prepare the sql query
    $sql = "INSERT INTO users (FirstName, Surname, Password, Email, Salt)
            VALUES ('$firstname', '$surname', '$hashed_pass', '$email', '$salt')";

    if ($conn->query($sql) === TRUE) {

        $link = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $link = str_replace("attempt.php", "verify.php?email=" . $emailVal, $link);

        //$recipient = "cm2147@bath.ac.uk";
        $recipient = "cm2147@bath.ac.uk";
        //$recipient = $emailVal;

        sendActivationMail($recipient, $link);

        //echo mail("charlesmaynard@mail.com","Hypertude Signup","This is an automated email do not reply\n\nClick the link below to activate your account\n\n".$link, "From: cm2147@bath.ac.uk");

        return 21;
    } else {
        return 12;
    }
}

/**
 * Sends an email to the given email from the no-reply account
 * @param $recipient The email receiving the message
 * @param $message The message to be sent
 */
function sendActivationMail($recipient, $message)
{
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 1;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'Smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'hypertudeservice@gmail.com';                 // SMTP username
        $mail->Password = 'HyperTude';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('hypertudeservice@gmail.com', 'no-reply-hypertude');
        $mail->addAddress($recipient);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Hypertude Signup';

        $mail->Body = "<html><body>" . "Your hypertude account is ready for activation<br>If this is your email: " . $recipient . "<br>Then click the link below<br><br><a href=\"http://" . $message . "\">" . "Activate your account here</body></html>";
        $mail->AltBody = "http://" . $message;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}