<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as a proctor, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "proctor") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Check if form is submitted for editing an assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["assignment_id"])) {
    // Get the form data
    $assignment_id = $_POST["assignment_id"];
    $student_username = $_POST["student_username"];
    $student_name = $_POST["student_name"];
    $student_contact_number = $_POST["student_contact_number"];
    
    // Check if password is provided and hash it
    if (!empty($_POST["student_password"])) {
        $student_password = password_hash($_POST["student_password"], PASSWORD_DEFAULT);
    } else {
        // If password is not provided, fetch the existing hashed password
        $sql_password = "SELECT s.password FROM student s INNER JOIN dormitory_assignments da ON s.student_id = da.student_id WHERE da.assignment_id = ?";
        $stmt_password = $conn->prepare($sql_password);
        $stmt_password->bind_param("i", $assignment_id);
        $stmt_password->execute();
        $result_password = $stmt_password->get_result();
        $row_password = $result_password->fetch_assoc();
        $student_password = $row_password["password"];
    }

    // Update the student details in the database
    $sql_update = "UPDATE student 
                   SET username = ?, name = ?, contact_number = ?, password = ?
                   WHERE student_id = (
                       SELECT student_id 
                       FROM dormitory_assignments 
                       WHERE assignment_id = ?
                   )";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssssi", $student_username, $student_name, $student_contact_number, $student_password, $assignment_id);
    if ($stmt->execute()) {
        // Redirect back to the assignment management page or display a success message
        header("Location: manage_students.php");
        exit;
    } else {
        // Handle the error
        echo "Error updating student details: " . $stmt->error;
    }
}

// Fetch dormitory assignments for the proctor's block along with additional information
$proctor_block_id = $_SESSION["block_id"];
$sql_assignments = "SELECT da.assignment_id, s.username AS student_username, s.name AS student_name, s.password AS student_password, s.contact_number AS student_contact_number, 
                            s.gender AS student_gender, s.batch AS student_batch, bl.block_name, r.room_number, 
                            da.assignment_start_date, da.assignment_end_date
                    FROM dormitory_assignments da
                    INNER JOIN beds b ON da.bed_id = b.bed_id
                    INNER JOIN rooms r ON b.room_id = r.room_id
                    INNER JOIN blocks bl ON r.block_id = bl.block_id
                    INNER JOIN student s ON da.student_id = s.student_id
                    WHERE bl.block_id = ?";
$stmt = $conn->prepare($sql_assignments);
$stmt->bind_param("i", $proctor_block_id);
$stmt->execute();
$result_assignments = $stmt->get_result();

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Student Assignments</h2>
        </div>
        <div class="card-body">
            <!-- Display assignments -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student Username</th>
                            <th>Student Name</th>
                            <th>Contact Number</th>
                            <th>Gender</th>
                            <th>Batch</th>
                            <th>Block Name</th>
                            <th>Room Number</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_assignments->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row['student_username']; ?></td>
                                <td><?php echo $row['student_name']; ?></td>
                                <td><?php echo $row['student_contact_number']; ?></td>
                                <td><?php echo $row['student_gender']; ?></td>
                                <td><?php echo $row['student_batch']; ?></td>
                                <td><?php echo $row['block_name']; ?></td>
                                <td><?php echo $row['room_number']; ?></td>
                                <td><?php echo $row['assignment_start_date']; ?></td>
                                <td><?php echo $row['assignment_end_date']; ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editAssignmentModal<?php echo $row['assignment_id']; ?>">
                                        Edit
                                    </button>
                                    <!-- Modal for editing assignment -->
                                    <div class="modal fade" id="editAssignmentModal<?php echo $row['assignment_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editAssignmentModalLabel<?php echo $row['assignment_id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editAssignmentModalLabel<?php echo $row['assignment_id']; ?>">Edit Assignment</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="assignment_id" value="<?php echo $row['assignment_id']; ?>">
                                                        <div class="form-group">
                                                            <label for="student_username">Student Username</label>
                                                            <input type="text" class="form-control" id="student_username" name="student_username" value="<?php echo $row['student_username']; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="student_name">Student Name</label>
                                                            <input type="text" class="form-control" id="student_name" name="student_name" value="<?php echo $row['student_name']; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="student_contact_number">Student Contact Number</label>
                                                            <input type="text" class="form-control" id="student_contact_number" name="student_contact_number" value="<?php echo $row['student_contact_number']; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="student_password">Student Password</label>
                                                            <input type="password" class="form-control" id="student_password" name="student_password" >
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>
