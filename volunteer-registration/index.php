
<?php
session_start();
include '../db/connect.php'; // Assuming this file gives you $conn
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token.';
    }

    // Sanitize input data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($mobile) || empty($password) || empty($confirm_password)) {
        $errors[] = 'All fields are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if (!preg_match('/^\d{10}$/', $mobile)) {
        $errors[] = 'Invalid mobile number.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    // Strong password validation
    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) ||    // At least one uppercase letter
        !preg_match('/[a-z]/', $password) ||    // At least one lowercase letter
        !preg_match('/\d/', $password) ||       // At least one digit
        !preg_match('/[\W_]/', $password)       // At least one special character
    ) {
        $errors[] = 'Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.';
    }

    // Check if email or mobile already exists in the database
    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT * FROM volunteers WHERE email = ? OR mobile = ?');
        
        // Using PDO's bindValue method instead of bind_param
        $stmt->bindValue(1, $email, PDO::PARAM_STR);
        $stmt->bindValue(2, $mobile, PDO::PARAM_STR);
        
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            $errors[] = 'Email or Mobile number already taken.';
        }
    }

    // If no errors, generate OTP and proceed to OTP verification
    if (empty($errors)) {
        function generateOTP($length = 6) {
            return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        }

        $_SESSION['otp'] = generateOTP();
        $_SESSION['otp_expiration'] = time() + 300; // 5-minute expiration
        $_SESSION['form_data'] = [
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        // Redirect to OTP verification page
        header('Location: ./email.php');
        exit();
    } else {
        $_SESSION['error'] = $errors; // Store all errors in session
    }
}
?>

<?php
$page_title = "Volunteers Registration"; // Define page title dynamically
include '../header/index.php';  // Include header
?>

<header>
    <style>
        .registration-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
        }
        .registration-card h3 {
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
    </style>
</header>
<body>
    <div class="registration-card d-block mx-auto my-5">
        <h3>Volunteer Registration</h3>

        <!-- Display Error Messages if Available -->
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($_SESSION['error'] as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['error']); ?> <!-- Clear errors after displaying -->
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="mobile" class="form-label">Mobile Number</label>
                <input type="text" class="form-control" id="mobile" name="mobile" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <span class="input-group-text">
                        <i class="bi bi-eye" id="togglePassword" onclick="togglePasswordVisibility('password', 'togglePassword')"></i>
                    </span>
                </div>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <span class="input-group-text">
                        <i class="bi bi-eye" id="toggleConfirmPassword" onclick="togglePasswordVisibility('confirm_password', 'toggleConfirmPassword')"></i>
                    </span>
                </div>
            </div>

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
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
    function preventBackNavigation() {
        // Replace the current history state to prevent the user from going back
        window.history.replaceState(null, '', window.location.href);
    
        // Add an event listener to handle back button presses
        window.addEventListener('popstate', function (event) {
            // Redirect user to the login page if they try to go back
            window.location.href = 'https://exams.swizosoft.com/login';  // Hardcoded login page URL
        });
    }
    
    // Call the function when you load the login page
    preventBackNavigation();

</script>
