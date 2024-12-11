<?php
session_start();
include '../db/connect.php';

function isValidCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
}

function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $csrf_token = $_POST['csrf_token'];

    if (!isValidCsrfToken($csrf_token)) {
        $error_message = 'Invalid CSRF token';
    } else {
        $currentDate = date('Y-m-d');

        if ($role === 'admin') {
            $stmt = $conn->prepare("SELECT * FROM admin WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $user);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['mobile'] = $user['mobileNumber'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                header("Location: ../admin-dashboard");
                exit();
            } else {
                $error_message = "Invalid admin credentials!";
            }

        } elseif ($role === 'user') {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $user);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['mobile'] = $user['mobile'];
                $_SESSION['email'] = $user['email'];
                header("Location: ../volunteer-dashboard");
                exit();
            } else {
                $error_message = "Invalid user credentials!";
            }

        } elseif ($role === 'company') {
            $stmt = $conn->prepare("SELECT * FROM company WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] === 'Approved') {
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['mobile'] = $user['mobileNumber'];
                    $_SESSION['email'] = $user['email'];
                    header("Location: ../company-dashboard");
                    exit();
                } else {
                    $error_message = "Your application has not been approved yet.";
                }
            } else {
                $error_message = "Invalid email or password.";
            }

        } else {
            $error_message = "Invalid role selected!";
        }
    }
}
?>


<?php
$page_title = "Login"; 
include '../header/index.php';  
?>
<header>
    <style>
        /*body {*/
        /*    background-color: #f8f9fa;*/
            
        /*    display: flex;*/
        /*    justify-content: center;*/
        /*    align-items: center;*/
        /*    height: 100vh;*/
        /*}*/
        
        .login-card {
            
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
        }
        .login-card h3 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</header>
<body>
    <div class="login-card d-block mx-auto my-5">
        <h3>Login</h3>


        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>


            <div class="mb-3">
                <label for="role" class="form-label">Login as</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="" selected disabled>Select role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                    <option value="company">Company</option>
                </select>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    <div class="text-center mt-2">
        <a href="../forgot-password" class="text-decoration-none">Forgot your password?</a>
    </div>
</div>

    </div>


<?php
include '../footer/index.php';  
?>
<script>
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");
    const eyeIcon = document.querySelector("#eyeIcon");

    togglePassword.addEventListener("click", function () {
       
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);

        if (type === "password") {
            eyeIcon.classList.remove("bi-eye-slash");
            eyeIcon.classList.add("bi-eye");
        } else {
            eyeIcon.classList.remove("bi-eye");
            eyeIcon.classList.add("bi-eye-slash");
        }
    });
</script>
