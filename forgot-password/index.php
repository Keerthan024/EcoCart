<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

// Include the database connection
include '../db/connect.php'; // This file contains the $conn PDO variable

// Function to generate a random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Function to send password reset email
function sendResetEmail($email, $name, $token) {
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
        $mail->Port       = 587;                                    //TCP port to connect to
        
        //Recipients
        $mail->setFrom('voluntrix1@gmail.com', 'Voluntrix');
        $mail->addAddress($email, $name);     //Add a recipient
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = "Password Reset Link";
        $mail->Body    = "Hello $name,<br><br>"
                        . "You have requested to reset your password. "
                        . "Please click the following link to reset your password:<br><br>"
                        . "<a href='http://192.168.171.220/mini-proj/update-password/?token=$token'>Reset Password</a><br><br>"
                        . "This link will expire in 10 minutes.<br><br>"
                        . "If you did not request this, please ignore this email.<br><br>"
                        . "Regards,<br>"
                        . "Voluntrix Team";
    
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$errors = [];
$success = [];

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize the email input
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $role = $_POST['role']; // Get the selected role (Faculty/Student)

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format. Please enter a valid email address.";
    } elseif (empty($role)) {
        $errors[] = "Please select a role.";
    } else {
        // Select the appropriate table and column based on the role
        if ($role === 'admin') {
            $table = 'admin';
            $nameColumn = 'name'; // Column for faculty's name
        }
        elseif ($role === 'company') {
            $table = 'company';
            $nameColumn = 'name'; // Column for faculty's name
        }
        elseif ($role === 'volunteer') {
            $table = 'volunteers';
            $nameColumn = 'name'; // Column for faculty's name
        } else {
            $errors[]="Invalid Role Selected";
        }

        // Check if email exists in the selected table
        $stmt = $conn->prepare("SELECT id, $nameColumn FROM $table WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = "No user found with that email address.";
        } else {
            // Generate a unique token for password reset
            $token = generateToken();

            // Store token in the database with a timestamp for expiration (10 minutes)
            $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            $updateTokenStmt = $conn->prepare("UPDATE $table SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
            $updateTokenStmt->execute([$token, $expiry, $user['id']]);

            // Send email with password reset link
            if (sendResetEmail($email, $user[$nameColumn], $token)) {
                $success[] = "Password reset link sent to your email address.";
            } else {
                $errors[] = "Failed to send password reset email. Please try again later.";
            }
        }
    }
}
?>
<?php
$page_title = "Forgot Password"; // Define page title dynamically
include '../header/index.php';  // Include header
?>

<header>
    <style>
        .forgot-password-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
        }
        .forgot-password-card h3 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</header>
<body>
    <div class="forgot-password-card d-block mx-auto my-5">
        <h3>Forgot Password</h3>

        <!-- Display Error Message if Available -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $error) {
                    echo $error . "<br>";
                } ?>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <?php foreach ($success as $message) {
                    echo $message . "<br>";
                } ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Reset password as</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="" selected disabled>Select role</option>
                    <option value="admin">Admin</option>
                    <option value="company">Company</option>
                    <option value="volunteer">Volunteer</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Send Password Reset Link</button>
        </form>

        <div class="text-center mt-3">
            <a href="../login" class="text-decoration-none">Back to Login</a>
        </div>
    </div>
</body>
<?php
include '../footer/index.php';  // Include footer
?>
