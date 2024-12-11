<?php
session_start();
include '../../db/connect.php'; // Include database connection

$errors = [];

// Function to generate CSRF token
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Function to verify CSRF token
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $errors['csrf'] = 'Invalid CSRF token.';
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
            $formData = $_SESSION['form_data'];
            $name = $formData['name'];
            $email = $formData['email'];
            $mobile = $formData['mobile'];
            $password = $formData['password'];

            $sql = "INSERT INTO volunteers (name, email, mobile, password) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $email, $mobile, $password]);

            if ($stmt->rowCount() > 0) {
                // Clear session data
                unset($_SESSION['otp'], $_SESSION['otp_expiration'], $_SESSION['form_data'], $_SESSION['csrf_token']);

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
