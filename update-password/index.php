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

$errors = [];
$success = [];

// Handle token validation and password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['token']) && isset($_POST['role'])) {
        $token = $_GET['token'];
        $role = $_POST['role'];
        
        // Select table based on role
        $table = ($role === 'admin') ? 'admin' : (($role === 'company') ? 'company' :'volunteers');
        $column = ($role === 'admin') ? 'name' : (($role === 'company') ? 'name' :'name');

        // Validate token and get user information
        $stmt = $conn->prepare("SELECT * FROM $table WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = "Invalid token.";
        } else {
            // Check token expiry
            $expiryTimestamp = strtotime($user['reset_token_expiry']);
            $currentTimestamp = time();

            if ($currentTimestamp > $expiryTimestamp) {
                $errors[] = "Token has expired.";
            } else {
                // Process password update
                $newPassword = $_POST['new-password'];
                $confirmNewPassword = $_POST['confirm-new-password'];

                // Validate new password
                if ($newPassword !== $confirmNewPassword) {
                    $errors[] = "New passwords do not match.";
                } elseif (!validatePassword($newPassword)) {
                    $errors[] = "Password must be at least 8 characters long, with uppercase, lowercase, a number, and a special character.";
                } else {
                    // Update password in database
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE $table SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
                    $updateStmt->execute([$hashedPassword, $user['id']]);

                    if ($updateStmt->rowCount() > 0) {
                        // Send password recovery confirmation email
                        if (sendRecoveryConfirmationEmail($user['email'], $user[$column])) {
                            $success[] = "Password updated successfully. Check your email for confirmation.";
                        } else {
                            $errors[] = "Password updated, but failed to send confirmation email.";
                        }
                    } else {
                        $errors[] = "Failed to update password. Please try again.";
                    }
                }
            }
        }
    } else {
        $errors[] = "Token or role not provided.";
    }
}

// Function to validate password strength
function validatePassword($password) {
    return strlen($password) >= 8 &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/[0-9]/', $password) &&
           preg_match('/[!@#$%^&*]/', $password);
}

// Function to send password recovery confirmation email
function sendRecoveryConfirmationEmail($email, $name) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'voluntrix1@gmail.com';
        $mail->Password   = 'msrj zfml clxd wvri';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        
        //Recipients
        $mail->setFrom('voluntrix1@gmail.com', 'Voluntrix');
        $mail->addAddress($email, $name);

        //Content
        $mail->isHTML(true);
        $mail->Subject = "Password Updated";
        $mail->Body    = "Hello $name,<br><br>"
                        . "Your password has been successfully updated.<br><br>"
                        . "If you did not make this change, please contact us immediately.<br><br>"
                        . "Regards,<br>"
                        . "Voluntrix Team";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>

<?php
$page_title = "Reset Password"; // Define page title dynamically
include '../header/index.php';  // Include header
?>
<head>
    <style>


        .reset-password-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
        }

        .reset-password-card h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-toggle-password {
            cursor: pointer;
        }

        /* Footer positioning */
        footer {
            position: relative;
            bottom: 0;
            width: 100%;
            text-align: center;
            margin-top: auto;
            padding: 10px 0;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="reset-password-card d-block mx-auto my-5">
        <h3>Reset Password</h3>

        <!-- Display Success Message if Available -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <?php foreach ($success as $message) {
                    echo $message . '<br>';
                } ?>
            </div>
        <?php endif; ?>

        <!-- Display Error Messages if Available -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $error) {
                    echo $error . '<br>';
                } ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="new-password" class="form-label">New Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="new-password" name="new-password" required>
                    <button class="btn btn-outline-secondary btn-toggle-password" type="button" id="toggleNewPassword">
                        <i class="bi bi-eye" id="newPasswordEyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label for="confirm-new-password" class="form-label">Confirm New Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm-new-password" name="confirm-new-password" required>
                    <button class="btn btn-outline-secondary btn-toggle-password" type="button" id="toggleConfirmPassword">
                        <i class="bi bi-eye" id="confirmPasswordEyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Reset as</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="" selected disabled>Select role</option>
                    <option value="admin">Admin</option>
                    <option value="volunteer">Volunteer</option>
                    <option value="company">Company</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        </form>

        <div class="text-center mt-2">
            <a href="../login/" class="text-decoration-none">Back to Login</a>
        </div>
    </div>

<?php
include '../footer/index.php';  // Include footer
?>

<script>
    const toggleNewPassword = document.querySelector("#toggleNewPassword");
    const newPassword = document.querySelector("#new-password");
    const newPasswordEyeIcon = document.querySelector("#newPasswordEyeIcon");

    const toggleConfirmPassword = document.querySelector("#toggleConfirmPassword");
    const confirmPassword = document.querySelector("#confirm-new-password");
    const confirmPasswordEyeIcon = document.querySelector("#confirmPasswordEyeIcon");

    toggleNewPassword.addEventListener("click", function () {
        const type = newPassword.getAttribute("type") === "password" ? "text" : "password";
        newPassword.setAttribute("type", type);
        newPasswordEyeIcon.classList.toggle("bi-eye-slash");
        newPasswordEyeIcon.classList.toggle("bi-eye");
    });

    toggleConfirmPassword.addEventListener("click", function () {
        const type = confirmPassword.getAttribute("type") === "password" ? "text" : "password";
        confirmPassword.setAttribute("type", type);
        confirmPasswordEyeIcon.classList.toggle("bi-eye-slash");
        confirmPasswordEyeIcon.classList.toggle("bi-eye");
    });
</script>
