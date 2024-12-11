<?php
session_start();
if(empty($_SESSION['id'])||empty($_SESSION['mobile'])||empty($_SESSION['email'])){
    header('location: ../login');
    exit();
}
else{

try {


    $pdo = new PDO('mysql:host=localhost;dbname=volunteers', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        die("Admin not found"); 
        // header('location: ../login');
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="https://www.swizosoft.com/images/swizosoft.jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    
</head>

<body>
    <div class="sidebar">
        <a href="#">Dashboard</a>
        <a href="#profile">Profile</a>
        <a href="./update-password">Settings</a>
        <a href="./logout.php">Logout</a>
    </div>

    <div class="main-content" id="main-content">
        <div class="header">
            <h1>Hey <?php echo htmlspecialchars($admin['name']); ?>! Welcome To EcoCart Admin's Panel</h1>
            <div class="theme-switcher">
                <button onclick="toggleTheme()">Toggle Theme</button>
            </div>
        </div>

        <div class="profile-card" id="profile">
            <img src="avatar.png" alt="Admin Profile Picture">
            <div class="profile-info">
            <p><strong>Admin ID:</strong> <?php echo htmlspecialchars($admin['id']); ?></p>
            <p><strong>Username:</strong> <?php $_SESSION['name']=$admin['name']; echo htmlspecialchars($admin['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
            </div>
        </div>

        <div class="options">
           
            <div class="option-card" id="manage-users" >
                <i class="fas fa-pen-alt"></i>
                <h4>Verify Company</h4>
                <div class="theme-switcher" style="padding:5%; margin:5%; ">
                    <form action="" method="post">
                        <button name="add">Verify Now!</button>
                    </form>
                    <?php 
                        if(isset($_POST["add"])){
                            echo '<script>window.location.href = "../company-approval";</script>';
                            exit();
                        }
                    ?>
                </div>
            </div>
        </div>


        <div class="time" id="time"></div>
    </div>

    <div class="footer">
        &copy; 2024 EcoCart Management Authority. All Rights Reserved.
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        function updateTime() {
            const timeElement = document.getElementById('time');
            const now = new Date();
            timeElement.innerText = now.toLocaleTimeString();
        }

        // Update time every second
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>

</html>
<?php
}
exit();
?>
