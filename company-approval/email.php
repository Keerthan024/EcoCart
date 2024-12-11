<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function

if(empty($email=$_SESSION['email_faculty_id'])){
    session_unset();
    session_destroy();
    header("location:./");
    exit();
}else{
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'voluntrix1@gmail.com';                     //SMTP username
    $mail->Password   = 'msrj zfml clxd wvri';                               //SMTP password
    $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    
    $email=$_SESSION['email_faculty_id'];
    
    
    $mail->setFrom('voluntrix1@gmail.com', 'Voluntrix');
    $mail->addAddress($email, $email);     //Add a recipient

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Application Status - '.$_SESSION['email_status'];
        $mail->Body    = $_SESSION['message'];
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    $mail->send();
    header("location: ./");
} catch (Exception $e) {
    die("Something went wrong in sending the mail.");
}
}

?>