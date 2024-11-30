<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UEE1 Educational System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="../img/logo.png" alt="UEE1 Logo" class="img-fluid" style="max-height: 50px;">
                    UEE1 Educational System
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION["user_type"])) { ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user"></i> <?php echo $_SESSION["username"]; ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <?php if ($_SESSION["user_type"] == "admin") { ?>
                                        <a class="dropdown-item" href="../admin/admin_dashboard.php">Admin Dashboard</a>
                                        <!-- Add more admin-specific options -->
                                    <?php } elseif ($_SESSION["user_type"] == "manager") { ?>
                                        <a class="dropdown-item" href="../manager/manager_dashboard.php">College Instructor Dashboard</a>
                                        <!-- Add more manager-specific options -->
                                    <?php } elseif ($_SESSION["user_type"] == "proctor") { ?>
                                        <a class="dropdown-item" href="../proctor/proctor_dashboard.php">Department Instructor Dashboard</a>
                                        <!-- Add more proctor-specific options -->
                                    <?php } elseif ($_SESSION["user_type"] == "student") { ?>
                                        <a class="dropdown-item" href="../student/student_dashboard.php">Student Dashboard</a>
                                        <!-- Add more student-specific options -->
                                    <?php } ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="../logout.php">Logout</a>
                                </div>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content Area -->
    <main>
        <div class="container my-5">
            <h1 class="text-center mb-5">Welcome to UEE1 Educational System</h1>
            <p class="lead text-center mb-5">Explore our wide range of educational resources and services.</p>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Courses</h5>
                            <p class="card-text">Browse our diverse course offerings and enroll today.</p>
                            <a href="#" class="btn btn-outline-light">Explore Courses</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card bg-secondary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Events</h5>
                            <p class="card-text">Stay up-to-date with our upcoming events and workshops.</p>
                            <a href="#" class="btn btn-outline-light">Explore Events</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Resources</h5>
                            <p class="card-text">Discover a wealth of educational resources and materials.</p>
                            <a href="#" class="btn btn-outline-light">Explore Resources</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-primary text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2024 UEE1 Educational System. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#" class="text-white">About</a></li>
                        <li class="list-inline-item"><a href="#" class="text-white">Contact</a></li>
                        <li class="list-inline-item"><a href="#" class="text-white">Privacy Policy</a></li>
                        <li class="list-inline-item"><a href="#" class="text-white">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>