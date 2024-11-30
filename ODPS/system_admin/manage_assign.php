<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as system_admin, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "system_admin") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Function to assign students to dormitories based on their department, gender, and batch
function assignStudentsToDormitories($assignment_start_date, $assignment_end_date)
{
    global $conn;

    // Get all departments
    $sql_departments = "SELECT * FROM departments";
    $result_departments = $conn->query($sql_departments);

    // Loop through each department
    while ($department = $result_departments->fetch_assoc()) {
        // Get students of the department, sorted by batch (4 to 1)
        $department_id = $department['department_id'];
        $sql_students = "SELECT * FROM student WHERE department_id = $department_id ORDER BY batch DESC";
        $result_students = $conn->query($sql_students);

        // Assign each student to dormitory, block, room, and bed based on gender
        while ($student = $result_students->fetch_assoc()) {
            // Determine dormitory type based on college type
            $college_id = $department['college_id'];
            $sql_college_type = "SELECT college_type FROM colleges WHERE college_id = $college_id";
            $result_college_type = $conn->query($sql_college_type);
            $college_type = $result_college_type->fetch_assoc()['college_type'];

            // Map college type to dormitory type
            switch ($college_type) {
                case 'Natural':
                    $dormitory_type = 'Main';
                    break;
                case 'Social':
                    $dormitory_type = 'FBE';
                    break;
                default:
                    $dormitory_type = 'Main';
                    break;
            }

            // Append gender to dormitory type
            $dormitory_type .= ($student['gender'] == 'Male') ? ' Male' : ' Female';

            // Get available bed in the dormitory of the student's gender
            $sql_bed = "SELECT beds.bed_id 
                        FROM beds 
                        INNER JOIN rooms ON beds.room_id = rooms.room_id 
                        INNER JOIN blocks ON rooms.block_id = blocks.block_id 
                        INNER JOIN dormitories ON blocks.dormitory_id = dormitories.dormitory_id 
                        WHERE dormitories.dormitory_type = '$dormitory_type' AND beds.student_id IS NULL 
                        LIMIT 1";
            $result_bed = $conn->query($sql_bed);

            // Check if a bed is available
            if ($result_bed->num_rows > 0) {
                $bed = $result_bed->fetch_assoc();
                $bed_id = $bed['bed_id'];

                // Assign the student to the bed
                $student_id = $student['student_id'];
                $sql_assign_bed = "UPDATE beds SET student_id = $student_id WHERE bed_id = $bed_id";
                $conn->query($sql_assign_bed);

                // Update dormitory assignment record with start and end dates
                $sql_assignment = "INSERT INTO dormitory_assignments (student_id, bed_id, assignment_start_date, assignment_end_date) VALUES ($student_id, $bed_id, '$assignment_start_date', '$assignment_end_date')";
                $conn->query($sql_assignment);
            }
        }
    }
}

// Function to remove all dormitory assignments
function removeAllAssignments()
{
    global $conn;

    // Remove all assignments from dormitory_assignments table
    $sql_remove_assignments = "DELETE FROM dormitory_assignments";
    $conn->query($sql_remove_assignments);

    // Set student_id to NULL for all beds
    $sql_clear_beds = "UPDATE beds SET student_id = NULL";
    $conn->query($sql_clear_beds);
}

// Function to fetch overall assignment details with pagination
function fetchAssignmentDetails($page, $items_per_page)
{
    global $conn;

    $offset = ($page - 1) * $items_per_page;

    $sql_assignment_details = "SELECT 
                                    da.assignment_id,
                                    s.name AS student_name,
                                    s.username AS student_username,
                                    d.dormitory_name,
                                    b.block_name,
                                    r.room_number,
                                    b.block_capacity AS room_capacity,
                                    da.assignment_start_date,
                                    da.assignment_end_date
                                FROM
                                    dormitory_assignments da
                                        INNER JOIN
                                    beds bd ON da.bed_id = bd.bed_id
                                        INNER JOIN
                                    rooms r ON bd.room_id = r.room_id
                                        INNER JOIN
                                    blocks b ON r.block_id = b.block_id
                                        INNER JOIN
                                    dormitories d ON b.dormitory_id = d.dormitory_id
                                        INNER JOIN
                                    student s ON da.student_id = s.student_id
                                ORDER BY da.assignment_start_date DESC
                                LIMIT $items_per_page OFFSET $offset";
    $result_assignment_details = $conn->query($sql_assignment_details);
    $assignment_details = [];
    if ($result_assignment_details->num_rows > 0) {
        while ($row = $result_assignment_details->fetch_assoc()) {
            $assignment_details[] = $row;
        }
    }
    return $assignment_details;
}

// Include header
include_once '../includes/header.php';

// Pagination variables
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$items_per_page = 30;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['assign_students'])) {
        // Get assignment start and end dates
        $assignment_start_date = $_POST['assignment_start_date'];
        $assignment_end_date = $_POST['assignment_end_date'];

        // Assign students to dormitories
        assignStudentsToDormitories($assignment_start_date, $assignment_end_date);
    } elseif (isset($_POST['remove_assignments'])) {
        // Remove all assignments
        removeAllAssignments();
    }
}

// Pagination calculations
$sql_count = "SELECT COUNT(*) AS total_assignments FROM dormitory_assignments";
$result_count = $conn->query($sql_count);
$total_items = $result_count->fetch_assoc()['total_assignments'];
$total_pages = ceil($total_items / $items_per_page);

// Fetch assignment details for the current page
$assignment_details = fetchAssignmentDetails($page, $items_per_page);

?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Assignments</h2>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label for="assignment_start_date">Assignment Start Date:</label>
                    <input type="date" class="form-control" id="assignment_start_date" name="assignment_start_date"
                        required>
                </div>
                <div class="form-group">
                    <label for="assignment_end_date">Assignment End Date:</label>
                    <input type="date" class="form-control" id="assignment_end_date" name="assignment_end_date"
                        required>
                </div>
                <button type="submit" class="btn btn-primary" name="assign_students">Assign Students</button>
            </form>
        </div>
    </section>
    <section class="remove-assignments card mt-4">
        <div class="card-header">
            <h2 class="mb-0">Remove All Assignments</h2>
        </div>
        <div class="card-body">
            <form method="post">
                <button type="submit" class="btn btn-danger" name="remove_assignments">Remove All Assignments</button>
            </form>
        </div>
    </section>

    <section class="assignment-details card mt-4">
        <div class="card-header">
            <h2 class="mb-0">Overall Assignment Details</h2>
        </div>
        <div class="card-body">
        <div class="card-footer">
            <!-- Pagination -->
            <nav aria-label="Assignment navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link"
                                href="manage_assign.php?page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $page - 10);
                    $end_page = min($total_pages, $page + 9);
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        echo "<li class='page-item";
                        echo ($i == $page) ? " active" : "";
                        echo "'><a class='page-link' href='manage_assign.php?page={$i}'>{$i}</a></li>";
                    }
                    ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link"
                                href="manage_assign.php?page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Assignment ID</th>
                        <th>Student Name</th>
                        <th>Student Username</th>
                        <th>Dormitory Name</th>
                        <th>Block Name</th>
                        <th>Room Number</th>
                        <th>Room Capacity</th>
                        <th>Assignment Start Date</th>
                        <th>Assignment End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($assignment_details as $assignment) {
                        echo "<tr>";
                        echo "<td>{$assignment['assignment_id']}</td>";
                        echo "<td>{$assignment['student_name']}</td>";
                        echo "<td>{$assignment['student_username']}</td>";
                        echo "<td>{$assignment['dormitory_name']}</td>";
                        echo "<td>{$assignment['block_name']}</td>";
                        echo "<td>{$assignment['room_number']}</td>";
                        echo "<td>{$assignment['room_capacity']}</td>";
                        echo "<td>{$assignment['assignment_start_date']}</td>";
                        echo "<td>{$assignment['assignment_end_date']}</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    

    </section>


</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>