<?php
session_start();

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Clear cookies (if necessary)
if (isset($_COOKIE['PHPSESSID'])) {
    setcookie("PHPSESSID", "", time() - 3600, "/");
}

$page_title = "Success"; // Define page title dynamically
include '../../header/index.php';  // Include header
?>
<header>
    <style>
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-15px);
            }
        }

        .success-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 500px;
            margin: auto;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        .success-card h3 {
            margin-bottom: 20px;
            color: #28a745;
        }

        .success-icon {
            font-size: 60px;
            color: #28a745;
            animation: bounce 1s infinite;
        }

        .success-card p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .countdown {
            font-weight: bold;
            color: #007bff;
            font-size: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 10px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</header>
<body>
    <div class="success-card d-block mx-auto my-5">
        <!-- Bouncing checkmark icon -->
        <div class="success-icon">✔️</div>
        <h3>Registration Successful</h3>
        <p>Your registration has been successfully completed.</p>
        <p>Redirecting you to the <a href="../../login" target="_blank">login page</a> in <span id="countdown" class="countdown">5</span> seconds...</p>
        
        <button class="btn btn-primary" id="redirectBtn">OK</button>
    </div>

    <script>
        // Countdown and redirect logic
        var countdownElement = document.getElementById('countdown');
        var redirectBtn = document.getElementById('redirectBtn');
        var countdown = 5; // countdown starts at 5 seconds
        var countdownInterval = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            if (countdown === 0) {
                clearInterval(countdownInterval);
                window.location.href = "../../login";
            }
        }, 1000);

        // Redirect on button click
        redirectBtn.addEventListener('click', function() {
            clearInterval(countdownInterval);
            window.location.href = "../../login";
        });
    </script>
</body>
<?php
include '../../footer/index.php';  // Include footer
?>
