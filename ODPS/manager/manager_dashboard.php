<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as manager, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "manager") {
    header("Location: ../login.php");
    exit;
}

// Check if the dormitory_id is set in the session
if (!isset($_SESSION["dormitory_id"])) {
    echo "Error: Dormitory ID not set in the session.";
    exit;
}

// Get manager's dormitory ID
$manager_dormitory_id = $_SESSION["dormitory_id"];

// Fetch system statistics for the manager's dormitory
$total_rooms = 0;
$total_beds = 0;
$total_dormitory_assignments = 0;

// Query to fetch total rooms for the manager's dormitory
$sql = "SELECT COUNT(*) AS total_rooms
        FROM rooms
        JOIN blocks ON rooms.block_id = blocks.block_id
        JOIN dormitories ON blocks.dormitory_id = dormitories.dormitory_id
        WHERE dormitories.dormitory_id = $manager_dormitory_id";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_rooms = $result->fetch_assoc()["total_rooms"];
} else {
    echo "Error fetching total rooms: " . $conn->error;
}

// Query to fetch total beds for the manager's dormitory
$sql = "SELECT COUNT(*) AS total_beds
        FROM beds
        JOIN rooms ON beds.room_id = rooms.room_id
        JOIN blocks ON rooms.block_id = blocks.block_id
        JOIN dormitories ON blocks.dormitory_id = dormitories.dormitory_id
        WHERE dormitories.dormitory_id = $manager_dormitory_id";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_beds = $result->fetch_assoc()["total_beds"];
} else {
    echo "Error fetching total beds: " . $conn->error;
}

// Query to fetch total dormitory assignments for the manager's dormitory
$sql = "SELECT COUNT(*) AS total_dormitory_assignments
        FROM dormitory_assignments
        JOIN beds ON dormitory_assignments.bed_id = beds.bed_id
        JOIN rooms ON beds.room_id = rooms.room_id
        JOIN blocks ON rooms.block_id = blocks.block_id
        JOIN dormitories ON blocks.dormitory_id = dormitories.dormitory_id
        WHERE dormitories.dormitory_id = $manager_dormitory_id";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_dormitory_assignments = $result->fetch_assoc()["total_dormitory_assignments"];
} else {
    echo "Error fetching total dormitory assignments: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - Online Dormitory Placement System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Manager Dashboard</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total Rooms</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_rooms; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Total Beds</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_beds; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
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
