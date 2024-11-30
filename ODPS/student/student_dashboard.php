<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as student, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "student") {
    header("Location: ../login.php");
    exit;
}

// Include header
include_once '../includes/header.php';






// Function to fetch student details
function getStudentDetails($student_id, $conn)
{
    $sql = "SELECT s.name AS student_name,s.batch AS student_batch, s.username, c.college_name, d.department_name, s.gender, s.contact_number, b.bed_number, bl.block_name, r.room_number
            FROM student s
            INNER JOIN departments d ON s.department_id = d.department_id
            INNER JOIN colleges c ON d.college_id = c.college_id
            INNER JOIN beds b ON s.student_id = b.student_id
            INNER JOIN rooms r ON b.room_id = r.room_id
            INNER JOIN blocks bl ON r.block_id = bl.block_id
            WHERE s.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


// Get student ID from session
$student_id = $_SESSION['user_id'];

// Get student details
$student_details = getStudentDetails($student_id, $conn);



// Function to fetch the number of dormitory assignments for the student

function getAssignmentCount($student_id, $conn)
{
    $sql = "SELECT COUNT(*) AS assignment_count FROM dormitory_assignments WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['assignment_count'];
}

// Function to fetch the number of notices for the student
function getNoticeCount($conn)
{
    $sql = "SELECT COUNT(*) AS notice_count FROM notices";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['notice_count'];
}

// Function to fetch the number of comments made by the student
function getCommentCount($student_id, $conn)
{
    $sql = "SELECT COUNT(*) AS comment_count FROM comments WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['comment_count'];
}

// Function to get all roommates
function getAllRoommates($student_id, $conn)
{
    $sql = "SELECT s.name AS roommate_name, s.username AS roommate_username,s.batch AS roommate_batch, s.gender AS roommate_gender, s.contact_number AS roommate_contact_number,
                   c.college_name, d.department_name,
                   b.bed_number, blk.block_name, r.room_number
            FROM student s
            INNER JOIN beds b ON s.student_id = b.student_id
            INNER JOIN rooms r ON b.room_id = r.room_id
            INNER JOIN blocks blk ON r.block_id = blk.block_id
            INNER JOIN departments d ON s.department_id = d.department_id
            INNER JOIN colleges c ON d.college_id = c.college_id
            WHERE s.student_id != ? AND r.room_id IN (
                SELECT DISTINCT r1.room_id
                FROM beds b1
                INNER JOIN rooms r1 ON b1.room_id = r1.room_id
                WHERE b1.student_id = ?
            )
            ORDER BY s.name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $student_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


// Get student ID from session
$student_id = $_SESSION['user_id'];

// Get counts
$assignment_count = getAssignmentCount($student_id, $conn);
$notice_count = getNoticeCount($conn);
$comment_count = getCommentCount($student_id, $conn);

// Get all roommates
$roommates = getAllRoommates($student_id, $conn);
?>

<main class="container mt-5">



    <section class="dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Student ID Card</div>
                    <div class="card-body">
                        <h5 class="card-title">Welcome, <?php echo $student_details['student_name']; ?>!</h5>
                        <p class="card-text">This is your student ID card. Below are your details:</p>
                        <ul class="list-group list-group-flush">
                        <li class="list-group-item">Name: <?php echo $student_details['student_name']; ?></li>
                        <li class="list-group-item">Batch: <?php echo $student_details['student_batch']; ?></li>
                            <li class="list-group-item">Username: <?php echo $student_details['username']; ?></li>
                            <li class="list-group-item">College: <?php echo $student_details['college_name']; ?></li>
                            <li class="list-group-item">Department: <?php echo $student_details['department_name']; ?>
                            </li>
                            <li class="list-group-item">Gender: <?php echo $student_details['gender']; ?></li>
                            <li class="list-group-item">Contact Number:
                                <?php echo $student_details['contact_number']; ?>
                            </li>
                            <li class="list-group-item">Bed Number: <?php echo $student_details['bed_number']; ?></li>
                            <li class="list-group-item">Block Name: <?php echo $student_details['block_name']; ?></li>
                            <li class="list-group-item">Room Number: <?php echo $student_details['room_number']; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <section class="dashboard">

        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Total Dormitory Assignments</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $assignment_count; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total Notices</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $notice_count; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Total Comments Made</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $comment_count; ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="row bg-white  dashboard">
        <h3 class="card-header txt-center">Your Rommate</h3>

            <?php if ($roommates): ?>
                <?php foreach ($roommates as $roommate): ?>

                    <div class="col-md-4">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-header">Roommate: <?php echo $roommate['roommate_name']; ?></div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Name:</strong> <?php echo $roommate['roommate_name']; ?>
                                    </li>
                                    <li class="list-group-item"><strong>Username:</strong>
                                        <?php echo $roommate['roommate_username']; ?></li>
                                    <li class="list-group-item"><strong>Gender:</strong>
                                        <?php echo $roommate['roommate_gender']; ?></li>
                                    <li class="list-group-item"><strong>Contact Number:</strong>
                                        <?php echo $roommate['roommate_contact_number']; ?></li>
                                    <li class="list-group-item"><strong>College:</strong>
                                        <?php echo $roommate['college_name']; ?></li>
                                    <li class="list-group-item"><strong>Department:</strong>
                                        <?php echo $roommate['department_name']; ?></li>
                                        <li class="list-group-item"><strong>Batch:</strong>
                                        <?php echo $roommate['roommate_batch']; ?></li>
                                    <li class="list-group-item"><strong>Bed Number:</strong>
                                        <?php echo $roommate['bed_number']; ?></li>
                                    <li class="list-group-item"><strong>Block Name:</strong>
                                        <?php echo $roommate['block_name']; ?></li>
                                    <li class="list-group-item"><strong>Room Number:</strong>
                                        <?php echo $roommate['room_number']; ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">Roommates</div>
                        <div class="card-body">
                            <p>No roommates found.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </section>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>