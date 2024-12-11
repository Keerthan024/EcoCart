<?php
session_start();
include '../../db/connect.php'; // Include database connection

$errors = [];

// Function to generate CSRF token
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate CSRF token
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors['csrf'] = "Invalid CSRF token.";
    }

    $enteredOtp = htmlspecialchars(trim($_POST['otp']));

    // Validate OTP
    if (empty($enteredOtp)) {
        $errors['otp'] = "OTP is required.";
    } elseif (isset($_SESSION['otp'], $_SESSION['otp_expiration'])) {
        if (time() > $_SESSION['otp_expiration']) {
            $errors['otp'] = "OTP expired. Please request a new one.";
        } elseif ($_SESSION['otp'] !== $enteredOtp) {
            $errors['otp'] = "Invalid OTP. Please try again.";
        } else {
            // OTP is valid, proceed with data insertion
            $formData = $_SESSION['form_data'];
            $name = $formData['name'];
            $mobileNumber = $formData['mobileNumber'];
            $email = $formData['email'];
            $password = $formData['password'];
            $randomLogoName = $formData['randomLogoName'];
            // Move files from temp directory to final directory
            $logoDir = '../uploads/logo/';
            

            if (!file_exists($logoDir)) {
                mkdir($logoDir, 0777, true);
            }
            

            rename("../../uploads/temp/$randomLogoName", "$logoDir$randomLogoName");

            // Insert data into the database
            $sql = "INSERT INTO company (name, mobileNumber, email, logo, status, password) 
                    VALUES (?, ?, ?, ?, ?, ?)"; // Added plan column in SQL query
            $status = "Pending";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $name, $mobileNumber, $email, $randomLogoName, $status, $password  // Added plan in the execute parameters
            ]);

            if ($stmt->rowCount() > 0) {
                // Clear session data
                unset($_SESSION['otp'], $_SESSION['otp_expiration'], $_SESSION['form_data']);

                // Redirect to success page
                header("Location: ../success");
                exit();
            } else {
                $errors['general'] = "Error: Could not insert data into the database.";
            }
        }
    } else {
        $errors['otp'] = "OTP is missing or expired.";
    }
}
?>

<?php
$page_title = "OTP Verification"; // Define page title dynamically
include '../../header/index.php';  // Include header
?>
<header>
    <style>
        .otp-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
            margin: auto;
        }
        .otp-card h3 {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        .mb-3 {
            margin-bottom: 1rem;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</header>
<body>
    <div class="otp-card d-block mx-auto my-5">
        <h3>OTP Verification</h3>

        <!-- Display Error Messages if Available -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="otp" class="form-label">Enter OTP</label>
                <input type="text" class="form-control" id="otp" name="otp" required>
            </div>

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

            <button type="submit" class="btn btn-primary">Verify OTP</button>
        </form>
    </div>

<?php
include '../../footer/index.php';  // Include footer
?>
