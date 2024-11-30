<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as proctor, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "proctor") {
    header("Location: ../login.php");
    exit;
}

// Check if the block_id is set in the session
if (!isset($_SESSION["block_id"])) {
    echo "Error: Block ID not set in the session.";
    exit;
}

// Get proctor's block ID
$proctor_block_id = $_SESSION["block_id"];

// Fetch system statistics for the proctor's block
$total_rooms = 0;
$total_beds = 0;
$total_assignments = 0;

// Query to fetch total rooms for the proctor's block
$sql_rooms = "SELECT COUNT(*) AS total_rooms
              FROM rooms
              WHERE block_id = $proctor_block_id";
$result_rooms = $conn->query($sql_rooms);
if ($result_rooms !== false && $result_rooms->num_rows > 0) {
    $total_rooms = $result_rooms->fetch_assoc()["total_rooms"];
} else {
    echo "Error fetching total rooms: " . $conn->error;
}

// Query to fetch total beds for the proctor's block
$sql_beds = "SELECT COUNT(*) AS total_beds
             FROM beds
             JOIN rooms ON beds.room_id = rooms.room_id
             WHERE rooms.block_id = $proctor_block_id";
$result_beds = $conn->query($sql_beds);
if ($result_beds !== false && $result_beds->num_rows > 0) {
    $total_beds = $result_beds->fetch_assoc()["total_beds"];
} else {
    echo "Error fetching total beds: " . $conn->error;
}

// Query to fetch total assignments for the proctor's block
$sql_assignments = "SELECT COUNT(*) AS total_assignments
                    FROM dormitory_assignments
                    JOIN beds ON dormitory_assignments.bed_id = beds.bed_id
                    JOIN rooms ON beds.room_id = rooms.room_id
                    WHERE rooms.block_id = $proctor_block_id";
$result_assignments = $conn->query($sql_assignments);
if ($result_assignments !== false && $result_assignments->num_rows > 0) {
    $total_assignments = $result_assignments->fetch_assoc()["total_assignments"];
} else {
    echo "Error fetching total assignments: " . $conn->error;
}

// Fetch proctor's profile details
$proctor_profile = [];
if (isset($_SESSION["user_id"])) {
    $proctor_id = $_SESSION["user_id"];
    $sql_profile = "SELECT * FROM proctor WHERE proctor_id = $proctor_id";
    $result_profile = $conn->query($sql_profile);
    if ($result_profile !== false && $result_profile->num_rows > 0) {
        $proctor_profile = $result_profile->fetch_assoc();
    } else {
        echo "Error fetching proctor's profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proctor Dashboard - Online Dormitory Placement System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Proctor Dashboard</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo $proctor_profile['name']; ?></p>
                        <p><strong>Username:</strong> <?php echo $proctor_profile['username']; ?></p>
                        <p><strong>Contact Number:</strong> <?php echo $proctor_profile['contact_number']; ?></p>
                        <!-- You can display more profile details here -->
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total Rooms</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_rooms; ?></h5>
                    </div>
                </div>
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Total Beds</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_beds; ?></h5>
                    </div>
                </div>
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Assignments</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_assignments; ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>

</body>

</html>
