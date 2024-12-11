<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="icon" href="https://exams.swizosoft.com/logos/swizosoft.jpg" sizes="256x256" type="image/png" />
    <title><?php echo isset($page_title) ? $page_title : 'My Website'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Vibrant Header with Gradient */
        .navbar-custom {
            background: linear-gradient(90deg, #00c6ff, #0072ff); /* Gradient from light to dark blue */
        }
        .navbar-custom .navbar-brand, 
        .navbar-custom .nav-link {
            color: #ffffff;
        }
        .navbar-custom .nav-link:hover {
            color: #ffdd40; /* Hover color for links */
        }

        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .mt-auto {
            margin-top: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="../img2/img.jpg" alt="Logo" style="height: 50px;"> <!-- Replace 'path_to_logo/logo.png' with your actual logo file path -->
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/srinathon/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/srinathon/volunteer-registration">Volunteer's Registration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/srinathon/company-registration">Company Registration</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


