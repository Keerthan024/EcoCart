<?php
session_start();
include '../db/connect.php'; // Include database connection

$errors = [];

// Function to generate OTP
function generateOTP($length = 6) {
    return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $mobileNumber = htmlspecialchars(trim($_POST['mobileNumber']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password']; 
    $confirmPassword = $_POST['confirmPassword'];

    // Validate fields
    if (empty($name)) { $errors['facultyName'] = "Faculty name is required."; }
    if (empty($mobileNumber)) { $errors['mobileNumber'] = "Mobile number is required."; }
    if (empty($email)) { $errors['email'] = "Email is required."; }

    // Validate password strength
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/\d/', $password) || 
        !preg_match('/[\W_]/', $password)) {
        $errors['password'] = 'Password must be at least 8 characters long, and include an uppercase letter, a lowercase letter, a number, and a special character.';
    }

    // Confirm passwords match
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match.";
    }

    // Validate file uploads
    if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
        $errors['logo'] = "Logo is required and must be a valid file.";
    } else {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['logo']['type'], $allowedTypes)) {
            $errors['logo'] = "Logo must be an image (JPEG, PNG, GIF).";
        }
    }

    // Check if mobile number or email already exists in the database
    if (empty($errors)) {
        $checkQuery = "SELECT * FROM company WHERE mobileNumber = ? OR email = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->execute([$mobileNumber, $email]);

        if ($stmt->rowCount() > 0) {
            $errors['general'] = "Mobile number or email already exists.";
        } else {
            // Store form data in session
            $_SESSION['form_data'] = [
                'name' => $name,
                'mobileNumber' => $mobileNumber,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT), 
            ];

            // Move files to temporary folder and store their names in the session
            $uploadDir = '../uploads/temp/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
            }

            $randomLogoName = uniqid() . "_" . basename($_FILES['logo']['name']);

            // Move the uploaded files to the temporary folder
            if (move_uploaded_file($_FILES['logo']['tmp_name'], "$uploadDir$randomLogoName")) {

                // Save file names in session
                $_SESSION['form_data']['randomLogoName'] = $randomLogoName;

                // Generate OTP and save to session
                $_SESSION['otp'] = generateOTP();
                $_SESSION['otp_expiration'] = time() + 300; // OTP valid for 5 minutes
                $_SESSION['name'] = $name; 
                $_SESSION['email'] = $email; 

                // Redirect to email page for OTP validation
                header("Location: ./email.php");
                exit();
            } else {
                $errors['general'] = "Error: Could not upload files.";
            }
        }
    }
}

?>

<?php
$page_title = "Company/Institute Registration"; // Define page title dynamically
include '../header/index.php';  // Include header
?>

<header>
    <style>
        .application-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
        }

        .application-card h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .input-group .form-control {
            padding-right: 40px; /* To ensure space for the eye icon */
        }

        .input-group-text {
            background: transparent;
            border: none;
            cursor: pointer;
        }

        /* Styling for the download links */
        .download-links {
            margin-top: 30px;
        }

        .download-links .col {
            text-align: center;
        }

        .download-links a {
            display: block;
            width: 100%;
        }

        /* Mobile-specific adjustments */
        @media (max-width: 576px) {
            .application-card {
                padding: 20px;
            }
        }

    </style>
</header>

<body>
    <div class="container">
        <div class="application-card d-block mx-auto my-5">
            <h3>Company Registration</h3>

            <!-- Display Error Messages if Available -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="facultyName" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="mobileNumber" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="mobileNumber" name="mobileNumber" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>

                <!-- Password Field with Toggle Icon -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <span class="input-group-text">
                                <i class="bi bi-eye" id="togglePassword" onclick="togglePasswordVisibility('password', 'togglePassword')"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password Field with Toggle Icon -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            <span class="input-group-text">
                                <i class="bi bi-eye" id="toggleConfirmPassword" onclick="togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword')"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="paymentProof" class="form-label">Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept=".jpg,.jpeg,.png" required>
                    </div>
                </div>

                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="row mb-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php
include '../footer/index.php';  // Include footer
?>

<script>
    function togglePasswordVisibility(passwordFieldId, toggleIconId) {
        const passwordField = document.getElementById(passwordFieldId);
        const toggleIcon = document.getElementById(toggleIconId);
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove("bi-eye");
            toggleIcon.classList.add("bi-eye-slash");
        } else {
            passwordField.type = "password";
            toggleIcon.classList.remove("bi-eye-slash");
            toggleIcon.classList.add("bi-eye");
        }
    }
    

</script>
