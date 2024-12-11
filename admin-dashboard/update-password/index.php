<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

// Check if user is logged in
if (!isset($_SESSION['id'])||!isset($_SESSION['name'])) {
    header("Location: ../../login"); // Redirect to login page if not logged in
    exit();
}else{

$email = $_SESSION['email'];
$errors = [];
$success = [];

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=volunteers', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['current-password'];
    $newPassword = $_POST['new-password'];
    $confirmNewPassword = $_POST['confirm-new-password'];
    
    // Validate current password
    $userId = $_SESSION['id'];
    $stmt = $pdo->prepare("SELECT password FROM admin WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        $errors[] = "Current password is incorrect.";
    }
    if (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[a-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword) || !preg_match('/[!@#$%^&*]/', $newPassword)) {
        $errors[] = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
    }

    // Validate new password
    if ($newPassword != $confirmNewPassword) {
        $errors[] = "New passwords do not match.";
    }

    // Update password in database if no errors
    if (empty($errors)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE admin SET password = ? WHERE id = ?");
        $updateStmt->execute([$hashedPassword, $userId]);

        // Check if password update was successful
        if ($updateStmt->rowCount() > 0) {
            // Password updated successfully, send email notification
            try {
                $mail = new PHPMailer(true);
                
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'voluntrix1@gmail.com';
                $mail->Password   = 'msrj zfml clxd wvri';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Sender info
                $mail->setFrom('voluntrix1@gmail.com', 'Voluntrix');
                
                // Recipient
                $mail->addAddress($email, $_SESSION['name']);

                // Email content
                $mail->isHTML(true);
                $mail->Subject = "Password Update Notification";
                $mail->Body    = "Your password has been successfully updated.";

                // Send email
                $mail->send();
                $success[] = "Password Updated Successfully";
            } catch (Exception $e) {
                $errors[] = "Failed to send email: " . $mail->ErrorInfo;
            }
        } else {
            $errors[] = "Failed to update password.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="https://www.swizosoft.com/images/swizosoft.jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voluntrix - Update Password</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header">
        <h1>Voluntrix</h1>
        <div class="theme-switcher">
            <button onclick="toggleTheme()">Toggle Theme</button>
        </div>
    </div>

    <div class="container">
        <h2>Update Password</h2>
        <?php 
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<p class='error'>$error</p>";
                }
            }
        ?>
        <?php 
            if (!empty($success)) {
                foreach ($success as $suc) {
                    echo "<p class='success'>$suc</p>";
                }
            }
        ?>
        <form action="#" method="post">
            <input type="password" name="current-password" placeholder="Current Password" required>
            <input type="password" name="new-password" placeholder="New Password" required>
            <input type="password" name="confirm-new-password" placeholder="Confirm New Password" required>
            <button type="submit">Update Password</button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2024 Voluntrix. All rights reserved.</p>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle('light-mode');
            if (document.body.classList.contains('light-mode')) {
                localStorage.setItem('theme', 'light');
            } else {
                localStorage.setItem('theme', 'dark');
            }
        }

        // Apply the saved theme on page load
        window.addEventListener('load', () => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                document.body.classList.add('light-mode');
            }
        });
    </script>
</body>

</html>
<?php
}
?>