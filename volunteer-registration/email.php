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

if(empty($_SESSION['form_data']['email'])||empty($_SESSION['form_data']['email'])||empty($_SESSION['otp'])){
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
    $name=$_SESSION['form_data']['name'];
    $email=$_SESSION['form_data']['email'];
    $otp=$_SESSION["otp"];
    
    $mail->setFrom('voluntrix1@gmail.com', 'Voluntrix');
    $mail->addAddress($email, $name);     //Add a recipient

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Your OTP Code';
        $mail->Body    = "
            <html>
            <head>
                <style>
                    .container {
                        width: 70%;
                        margin: 0 auto;
                        font-family: Verdana, Geneva, Tahoma, sans-serif;
                        border: 1px solid #000;
                        padding: 5%;
                    }
                    footer {
                        text-align: center;
                        margin-top: 10%;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Welcome to Voluntrix - OTP Verification!</h2>
                    <br>
                    <br>
                    <h5>Dear $name,</h5>
                    <br>
                    <br>
                    Your OTP code for registration is: <strong>$otp</strong>
                    <br>
                    <br>
                    Please use this OTP to complete your registration process.
                    <br>
                    <br>
                    Best regards,
                    <br>
                    Voluntrix, Bengaluru-560074 
                    <br>
                    <footer>
                        <p>Bengaluru &copy; 2024-All rights are reserved</p>
                    </footer>
                </div>
            </body>
            </html>
        ";
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    $mail->send();
    header("location: ./otp-verification");
} catch (Exception $e) {
    die("Something went wrong in sending the mail.");
}
}

?>