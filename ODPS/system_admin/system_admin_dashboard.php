<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as admin, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "system_admin") {
    header("Location: ../login.php");
    exit;
}

// Fetch system statistics
$total_colleges = 0;
$total_departments = 0;
$total_students = 0;
$total_rooms = 0;

// Query to fetch total colleges
$sql = "SELECT COUNT(*) AS total_colleges FROM colleges";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_colleges = $result->fetch_assoc()["total_colleges"];
} else {
    echo "Error fetching total colleges: " . $conn->error;
}

// Query to fetch total departments
$sql = "SELECT COUNT(*) AS total_departments FROM departments";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_departments = $result->fetch_assoc()["total_departments"];
} else {
    echo "Error fetching total departments: " . $conn->error;
}

// Query to fetch total students
$sql = "SELECT COUNT(*) AS total_students FROM student";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_students = $result->fetch_assoc()["total_students"];
} else {
    echo "Error fetching total students: " . $conn->error;
}


// Query to fetch total students
$sql = "SELECT COUNT(*) AS total_managers FROM manager";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_managers = $result->fetch_assoc()["total_managers"];
} else {
    echo "Error fetching total students: " . $conn->error;
}
// Query to fetch total students
$sql = "SELECT COUNT(*) AS total_proctors FROM proctor";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_proctors = $result->fetch_assoc()["total_proctors"];
} else {
    echo "Error fetching total students: " . $conn->error;
}
// Query to fetch total students
$sql = "SELECT COUNT(*) AS total_blocks FROM blocks";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_blocks = $result->fetch_assoc()["total_blocks"];
} else {
    echo "Error fetching total students: " . $conn->error;
}
// Query to fetch total students
$sql = "SELECT COUNT(*) AS total_notices FROM notices";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_notices = $result->fetch_assoc()["total_notices"];
} else {
    echo "Error fetching total students: " . $conn->error;
}


// Query to fetch total rooms
$sql = "SELECT COUNT(*) AS total_rooms FROM rooms";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_rooms = $result->fetch_assoc()["total_rooms"];
} else {
    echo "Error fetching total rooms: " . $conn->error;
}



// Query to fetch total rooms
$sql = "SELECT COUNT(*) AS total_dormitories FROM dormitories";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_dormitories = $result->fetch_assoc()["total_dormitories"];
} else {
    echo "Error fetching total rooms: " . $conn->error;
}


// Query to fetch total rooms
$sql = "SELECT COUNT(*) AS total_beds FROM beds";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_beds = $result->fetch_assoc()["total_beds"];
}

else {
    echo "Error fetching total rooms: " . $conn->error;
} // Query to fetch total rooms
$sql = "SELECT COUNT(*) AS total_notices FROM notices";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_notices = $result->fetch_assoc()["total_notices"];
}

else {
    echo "Error fetching total rooms: " . $conn->error;
} // Query to fetch total rooms
$sql = "SELECT COUNT(*) AS total_dormitory_assignments FROM dormitory_assignments";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_dormitory_assignments = $result->fetch_assoc()["total_dormitory_assignments"];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Online Dormitory Placement System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Admin Dashboard</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Colleges</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_colleges; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Departments</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_departments; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total Students</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_students; ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Total Managers</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_managers; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Total Proctors</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_proctors; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Blocks</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_blocks; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Notices</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_notices; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total Rooms</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_rooms; ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Total Dormitories</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_dormitories; ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Total Beds</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_beds; ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Notices</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_notices; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Dormitory Assignments</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_dormitory_assignments; ?></h5>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <?php include '../includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>