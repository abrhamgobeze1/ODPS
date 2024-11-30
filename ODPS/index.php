<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Dormitory Placement System</title> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="shortcut icon" href="/odps/images/favicon/favicon.jpg" type="image/x-icon"> <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .hero-section {
            background-image: url('images/dormitory/campus.jpg');
            background-size: cover;
            color: #fff;
            text-align: center;
            padding: 100px 0;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 40px;
        }

        .features-section {
            padding: 80px 0;
            background-color: #fff;
            text-align: center;
        }

        .features-section h2 {
            font-size: 2.5rem;
            margin-bottom: 50px;
        }

        .feature {
            margin-bottom: 30px;
        }

        .feature h3 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .testimonial-section {
            padding: 80px 0;
            background-color: #f8f9fa;
            text-align: center;
        }

        .testimonial {
            margin-bottom: 50px;
        }

        .testimonial img {
            width: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .testimonial p {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .testimonial span {
            font-weight: bold;
            font-style: italic;
        }

        .carousel-item img {
            max-height: 400px;
            object-fit: cover;
        }
    </style>
</head>

<body> <?php include 'includes/header.php' ?>
    <section class="hero-section">
        <div class="container">
            <h1>Welcome to the Online Dormitory Placement System</h1>
            <p>Apply for your dorm room with ease</p> <a href="login.php"
                class="btn btn-success btn-lg">Login</a>
        </div>
    </section>
    <section class="features-section">
        <div class="container">
            <h2>Key Features</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature">
                        <h3>Streamlined Application</h3>
                        <p>Quick and easy application process.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature">
                        <h3>Convenient Room Selection</h3>
                        <p>Choose your preferred dorm room with ease.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature">
                        <h3>Secure Application Tracking</h3>
                        <p>Monitor your application status with confidence.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Slider -->
    <section>
        <div class="container">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></li>
                    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></li>
                    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></li>
                    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3"></li>
                    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="images/dormitory/dorm1.jpg" class="d-block w-100" alt="Dormitory 1">
                    </div>
                    <div class="carousel-item">
                        <img src="images/dormitory/dorm2.jpg" class="d-block w-100" alt="Dormitory 2">
                    </div>
                    <div class="carousel-item">
                        <img src="images/dormitory/dorm3.jpg" class="d-block w-100" alt="Dormitory 3">
                    </div>
                    <div class="carousel-item">
                        <img src="images/dormitory/dorm4.jpg" class="d-block w-100" alt="Dormitory 4">
                    </div>
                    <div class="carousel-item">
                        <img src="images/dormitory/dorm5.jpg" class="d-block w-100" alt="Dormitory 5">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <section class="testimonial-section">
        <div class="container">
            <h2>What Our Students Are Saying</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial"> <img src="images/testimonials/AVATARZ - Sheik.png" alt="Testimonial 1">
                        <p>"The online dormitory application was a breeze! I'm so happy with my new room."</p> <span>-
                            Fatuma Aden, First-year Student</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial"> <img src="images/testimonials/AVATARZ 2.png" alt="Testimonial 2">
                        <p>"This system made the entire process so smooth and effortless. I'm thrilled with my
                            placement!"</p> <span>- Amanuel Kebede, Sophomore</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial"> <img src="images/testimonials/AVATARZ - Tomas.png" alt="Testimonial 3">
                        <p>"The online platform is user-friendly and efficient. I can't imagine applying any other
                            way!"</p> <span>- Hiwot Desta, Junior</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial"> <img src="images/testimonials/AVATARZ 1.png" alt="Testimonial 4">
                        <p>"I'm so glad I used the online system. It made the entire process a breeze."</p> <span>-
                            Yohannes Alemayehu, Senior</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial"> <img src="images/testimonials/AVATARZ 4.png" alt="Testimonial 5">
                        <p>"The online dormitory placement system is user-friendly and efficient. I had no issues at
                            all."</p> <span>- Rahel Mekuria, Sophomore</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial"> <img src="images/testimonials/AVATARZ 3.png" alt="Testimonial 6">
                        <p>"I'm really impressed with the online application process. It made securing my dorm room a
                            breeze."</p> <span>- Eyob Tesfaye, First-year Student</span>
                    </div>
                </div>
            </div>
        </div>
    </section> <?php include 'includes/footer.php' ?> <!-- jQuery and Bootstrap JS -->


    <!-- Bootstrap JavaScript dependencies -->
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/chart.js"></script>
<script src="js/jquery-3.6.0.min.js"></script>
   
</body>
</html>