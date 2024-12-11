<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="icon" href="https://exams.swizosoft.com/logos/swizosoft.jpg" sizes="256x256" type="image/png" />
    <title>Examinations Authority of Swizosoft</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <style>
        /* Custom Styles */
        body {
            font-family: 'Roboto', sans-serif;
            color: #333;
        }
        h2, h3, h4, h5 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
         .slider-image {
        width: 100%;
        height: auto;  
        object-fit: cover;
    }
    .carousel-caption {
        background: rgba(0, 0, 0, 0.5);
        padding: 20px;
        border-radius: 10px;
    }
#testimonialsCarousel .carousel-item img {
    width: 150px; 
    height: 150px;
    object-fit: cover; 
    border-radius: 50%;
    margin-bottom: 15px;
    border: 4px solid #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}


    @media (max-width: 768px) {
        .carousel-item {
            height: 50vh; 
        }
        .carousel-caption h1 {
            font-size: 2.5rem; /* Adjust title size for smaller screens */
        }
        .carousel-caption p {
            font-size: 1.2rem; /* Adjust paragraph size for smaller screens */
        }
    }
        .team-member {
            text-align: center;
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 25px rgba(0,0,0,0.3);
        }
        .team-member img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 4px solid #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .plan-card {
            border: none;
            border-radius: 15px;
            padding: 30px 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            background: #fff;
        }
        .plan-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 30px rgba(0,0,0,0.2);
        }
        .plan-card h4 {
            color: #007bff;
        }
        .plan-card .display-6 {
            font-size: 2.5rem;
            color: #28a745;
        }
        .contact-form .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s;
        }
        .contact-form .btn-primary:hover {
            background-color: #0056b3;
        }
        .footer-icons a {
            font-size: 1.2rem;
            transition: color 0.3s;
        }
        .footer-icons a:hover {
            color: #007bff;
        }
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        /* Animation on Scroll */
        .animate__animated {
            animation-duration: 1s;
            animation-fill-mode: both;
        }
        /* Counter Styles */
        .counter {
            font-size: 2.5rem;
            color: #28a745;
            position: relative;
        }
        
        .counter::after {
            content: "+";
            position: absolute;
            right: -10px;
            top: 0;
            font-size: 1.5rem;
        }

    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="./img2/img.jpg" alt="EcoCart" height="50" width="60">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#slider">Home</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#notifications">Notifications</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="#vision-mission">About Us</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#team">Our Team</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="#trusted">Trusted By</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#plans">Plans</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./login">Sign In</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Slider Section -->
<div id="slider" class="carousel slide" data-bs-ride="carousel" style="margin-top: 56px;">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#slider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#slider" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <!--<button type="button" data-bs-target="#slider" data-bs-slide-to="2" aria-label="Slide 3"></button>-->
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="./img2/carousel2.jpg" class="d-block w-100 slider-image img-fluid" alt="Slide 1">
            <div class="carousel-caption d-none d-md-block">
                <h1 class="animate__animated animate__fadeInDown display-4 display-md-1">Welcome to EcoCart Management Authority</h1>
                <p class="animate__animated animate__fadeInUp lead">Your Partner in Event Success</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="./img2/carousel1.jpg" class="d-block w-100 slider-image img-fluid" alt="Slide 2">
            <div class="carousel-caption d-none d-md-block">
                <h1 class="animate__animated animate__fadeInDown display-4 display-md-1">Secure and Efficient Task Management for VMS</h1>
                <p class="animate__animated animate__fadeInUp lead">Ensuring Fairness and Integrity in EcoCart Management System Operations</p>
            </div>
        </div>
        <!--<div class="carousel-item">-->
        <!--    <img src="https://via.placeholder.com/1920x800.png?text=Innovative+Solutions+for+Education" class="d-block w-100 slider-image img-fluid" alt="Slide 3">-->
        <!--    <div class="carousel-caption d-none d-md-block">-->
        <!--        <h1 class="animate__animated animate__fadeInDown display-4 display-md-1">Innovative Solutions for Education</h1>-->
        <!--        <p class="animate__animated animate__fadeInUp lead">Empowering Institutions and Students</p>-->
        <!--    </div>-->
        <!--</div>-->
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#slider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#slider" data-bs-slide="next">
        <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>


    <!-- Vision and Mission Section -->
    <section id="vision-mission" class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h3>Our Vision</h3>
                    <p>Empowering organizations to efficiently manage their EcoCarts, enhance engagement through streamlined communication, and foster impactful community contributions through organized events and tasks.</p>
                </div>
                <div class="col-md-6">
                    <h3>Our Mission</h3>
                    <p>To provide a digital platform that simplifies EcoCart coordination and fosters collaboration between organizations and EcoCarts. Ensuring impactful community service and sustainable growth.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Trusted organization and EcoCarts Section -->
    <section id="trusted" class="bg-light py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-6 mb-4">
                    <i class="bi bi-building-fill text-primary" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">Trusted Organizations</h3>
                    <p id="colleges-count" class="display-6 counter">0</p>
                </div>
                <div class="col-md-6 mb-4">
                    <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">Trusted EcoCarts</h3>
                    <p id="students-count" class="display-6 counter">0</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Testimonials Section -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">What Our Clients Say</h2>
            <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                     <!--Testimonial 1 -->
                    <div class="carousel-item active" id="testimonial1">
                        <div class="d-flex flex-column align-items-center">
                            <img src="./img2/client_2.jpg" class="rounded-circle mb-3" alt="User 1">
                            <p class="text-center">"This platform has made managing our EcoCarts a smooth and hassle-free experience, ensuring maximum engagement with minimal effort."</p>
                            <h5>Mr. Ramesh Kulkarni</h5>
                            <small style="text-align: center">Manager, Helping Hands Foundation, Mangaluru</small>
                        </div>
                    </div>
                     <!--Testimonial 2 -->
                    <div class="carousel-item" id="testimonial2">
                        <div class="d-flex flex-column align-items-center">
                            <img src="./img2/client_5.avif" class="rounded-circle mb-3" alt="User 2">
                            <p class="text-center">"A simple yet powerful tool for organizing community services and EcoCart activities."</p>
                            <h5>Ms. Priya Nair</h5>
                            <small style="text-align: center">Coordinator, Green Mangaluru Initiative</small>
                        </div>
                    </div>
                     <!--Testimonial 3 -->
                    <div class="carousel-item" id="testimonial3">
                        <div class="d-flex flex-column align-items-center">
                            <img src="./img2/client_1.jpg" class="rounded-circle mb-3" alt="User 3">
                            <p class="text-center">"The EcoCart Management System has significantly improved our coordination and made tracking EcoCart hours easier than ever. Highly recommended!"</p>
                            <h5>Mr. Arjun Menon,</h5>
                            <small style="text-align: center">Project Manager, Mangaluru Community Outreach</small>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    
    <section id="contact" class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">Contact Us</h2>
            <div class="row">
               
                <div class="col-lg-6 mb-4">
                    <form class="contact-form" action="" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label"><i class="bi bi-person-fill me-2"></i>Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="bi bi-envelope-fill me-2"></i>Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label"><i class="bi bi-chat-left-text-fill me-2"></i>Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send-fill me-2"></i>Send Message</button>
                    </form>
                </div>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $message = htmlspecialchars($_POST['message']);

        
        $full_message = urlencode("Name: $name\nEmail: $email\nMessage: $message");

        
        $whatsapp_url = "https://wa.me/9480422194?text=$full_message";

        
        echo "<script>window.location.href = '$whatsapp_url';</script>";
    }
    ?>
                <div class="col-lg-6">
                    <h5><i class="bi bi-geo-alt-fill me-2"></i>Our Location</h5>
                    <p>Valachil, Mangaluru - 581344</p>
                    <h5 class="mt-4"><i class="bi bi-telephone-fill me-2"></i>Phone</h5>
                    <p>+91 9480422194</p>
                    <h5 class="mt-4"><i class="bi bi-envelope-fill me-2"></i>Email</h5>
                    <p>contact@ecocart.com</p>
                    <!-- <h5 class="mt-4"><i class="bi bi-clock-fill me-2"></i>Office Hours</h5>
                    <p>Monday - Friday: 9:00 AM - 6:00 PM</p> -->
                    <!-- Google Maps Embed -->
                    <!--<div class="mt-4">-->
                    <!--    <iframe src="https://www.google.com/maps/embed?pb=!1m18!..." width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2024 EcoCart Management Authority. All Rights Reserved.</p>
            <div class="footer-icons">
                <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
                <a href="#" class="text-white me-3"><i class="bi bi-linkedin"></i></a>
                <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies (Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Animate.css for Animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Optional JavaScript for additional functionality -->
    <script>
        // Optional: Initialize carousel animations
        document.addEventListener('DOMContentLoaded', () => {
            const carouselElements = document.querySelectorAll('.carousel-item');
            carouselElements.forEach((el) => {
                el.addEventListener('slide.bs.carousel', () => {
                    el.querySelectorAll('.animate__animated').forEach((animateEl) => {
                        animateEl.classList.remove('animate__fadeInDown', 'animate__fadeInUp');
                        void animateEl.offsetWidth; // Trigger reflow
                        animateEl.classList.add('animate__fadeInDown', 'animate__fadeInUp');
                    });
                });
            });
        });
         // Function to animate the counter
    function animateCounter(id, target, duration) {
        const element = document.getElementById(id);
        let start = 0;
        const increment = target / (duration / 10); // Update every 10ms
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                start = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(start);
        }, 10);
    }

    // Initialize counters when the Trusted section is in view
    document.addEventListener('DOMContentLoaded', () => {
        const trustedSection = document.getElementById('trusted');
        let countersStarted = false;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !countersStarted) {
                    animateCounter('colleges-count', 1, 2000); // 150 colleges over 2 seconds
                    animateCounter('students-count', 500, 3000); // 10,000 students over 3 seconds
                    countersStarted = true;
                }
            });
        }, {
            threshold: 0.5 // Trigger when 50% of the section is visible
        });

        observer.observe(trustedSection);
    });
    </script>
</body>
</html>
