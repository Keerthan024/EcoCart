<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="https://www.swizosoft.com/images/swizosoft.jpg">
    <title>Generate Certificate</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="header">
        <h1>Swizosoft</h1>
        <div class="theme-switcher">
            <button onclick="toggleTheme()">Toggle Theme</button>
        </div>
    </div>
    <div class="container">
        <h2>Download Your Certificate</h2>
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
        <form method="post">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <button type="submit">Generate Certificate</button>
        </form>
    </div>
    <div class="footer">
        <p>&copy; 2024 Swizosoft. All rights reserved.</p>
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
