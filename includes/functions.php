<?php

session_start();

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);


//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include('connection.php');
global $pdo;
$conn = $pdo->open();
$message = '';

require SITE_ROOT.'PHPMailer/vendor/autoload.php';

function redirect($path) {
    echo '<script>window.location.href = "'.$path.'"</script>';
}

function alert($text) {
    echo "<script>alert('{$text}')</script>";
}

function login() {
    global $conn;
    global $message;

    if(isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $query = 'SELECT * FROM users WHERE email = ? LIMIT 1';
        $stmt = $conn->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        //Check if username is correct
        if($user){
            // Check if password is correct
            if(password_verify($password, $user->pass)){
                //Storing user in session
                if($user->admin == 1) {
                    $_SESSION['admin'] = $user;
                    redirect('admin/index.php');
                } else {
                    $_SESSION['user'] = $user;
                    redirect('index.php');
                }
                redirect('index.php');
            }else{
                $message = '<p class="alert alert-danger">Email or password is wrong!</p>';
            }
        }else{
            $message = '<p class="alert alert-danger">Email or password is wrong!</p>';
        }
    }
}

function signup() {
    global $conn;
    global $message;

    if(isset($_POST['signup'])) {
        $name = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // PASSWORD TO HASHED PASSWORD
        $password = password_hash($password, PASSWORD_BCRYPT);

        $query = "SELECT * FROM users WHERE email = ? OR name = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$email, $name]);
        $user = $stmt->fetch();

        if(!empty($user)) {
            $message = '<p class="alert alert-danger">User already exists!</p>';
        } else {
            // SAVE DATA INTO DATABASE
            $query = 'INSERT INTO users(name, email, pass) VALUES (?,?,?)';
            $stmt = $conn->prepare($query);
            $res = $stmt->execute([$name, $email, $password]);

            if($res){
                $message = '<p class="alert alert-success">Signed up successfully!</p>';
            }else{
                $message = "Sorry! Registration not successful.";
            }
        }
    }
}

function sendMail($body, $to, $name, $subject, $email2='', $email3='') {

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
//         $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = SMTP_HOST;                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = USERNAME;                     //SMTP username
        $mail->Password = PASSWORD;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->CharSet = "utf8"; 

        //Recipients
        $mail->setFrom(USERNAME, SENDER);
        $mail->addAddress($to, $name);     //Add a recipient
        if(!empty($email2)) {
            $mail->addAddress($email2, $name);
        }
        if(!empty($email3)) {
            $mail->addAddress($email3, $name);
        }
        $mail->addReplyTo(USERNAME, SENDER);

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return $mailMsg = '<p class="alert alert-success mt-2">Email sent!</p>';
    } catch (Exception $e) {
        return $mailMsg = "<p class='alert alert-danger mt-2'>Email could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
    }
}

// Find Data by ID
function findById($table, $id) {
    global $conn;

    $query = "SELECT * FROM " . $table . " WHERE id = " . $id;
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch();

    return !empty($row) ? $row : false;
}

// Find All Data in a Table
function findAll($table) {
    global $conn;

    $query = "SELECT * FROM " . $table;
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    return !empty($rows) ? $rows : false;
}

// Find Single Data by Query
function findByQuery($query) {
    global $conn;

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch();

    return !empty($row) ? $row : false;
}

// Find All by Query
function findAllByQuery($query) {
    global $conn;

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    return !empty($rows) ? $rows : false;
}

?>