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

// Pagination
$limit = 4; // Number of assignments per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Check if form is submitted for adding or editing an allocation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_allocation'])) {
        // Add new allocation
        $student_id = $_POST['student_id'];
        $bed_id = $_POST['bed_id'];
        $assignment_start_date = $_POST['assignment_start_date'];
        $assignment_end_date = $_POST['assignment_end_date'];

        $sql = "INSERT INTO dormitory_assignments (student_id, bed_id, assignment_start_date, assignment_end_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $student_id, $bed_id, $assignment_start_date, $assignment_end_date);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_allocation'])) {
        // Edit existing allocation
        $student_id = $_POST['student_id'];
        $bed_id = $_POST['bed_id'];
        $assignment_start_date = $_POST['assignment_start_date'];
        $assignment_end_date = $_POST['assignment_end_date'];
        $assignment_id = $_POST['assignment_id'];

        $sql = "UPDATE dormitory_assignments SET student_id = ?, bed_id = ?, assignment_start_date = ?, assignment_end_date = ? WHERE assignment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissi", $student_id, $bed_id, $assignment_start_date, $assignment_end_date, $assignment_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_allocation'])) {
        // Delete existing allocation
        $assignment_id = $_POST['assignment_id'];

        $sql = "DELETE FROM dormitory_assignments WHERE assignment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Redirect to avoid resubmission on page refresh
    header("Location: $_SERVER[PHP_SELF]?page=$page");
    exit;
}

// Fetch assignments with pagination
$sql_assignments = "SELECT da.assignment_id, s.name AS student_name, r.room_number, b.bed_number, da.assignment_start_date, da.assignment_end_date
                   FROM dormitory_assignments da
                   INNER JOIN beds b ON da.bed_id = b.bed_id
                   INNER JOIN rooms r ON b.room_id = r.room_id
                   INNER JOIN student s ON da.student_id = s.student_id
                   LIMIT ?, ?";
$stmt = $conn->prepare($sql_assignments);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result_assignments = $stmt->get_result();
$assignments = $result_assignments->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count total number of assignments
$sql_count = "SELECT COUNT(*) AS total FROM dormitory_assignments";
$result_count = $conn->query($sql_count);
$total_assignments = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_assignments / $limit);

// Fetch students
$sql_students = "SELECT * FROM student";
$result_students = $conn->query($sql_students);
if ($result_students) {
    $students = $result_students->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error fetching students: " . $conn->error; // Output error message
    $students = []; // Set students array to empty
}

// Fetch beds
$sql_beds = "SELECT b.bed_id, b.bed_number, r.room_number FROM beds b INNER JOIN rooms r ON b.room_id = r.room_id";
$result_beds = $conn->query($sql_beds);
if ($result_beds) {
    $beds = $result_beds->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error fetching beds: " . $conn->error; // Output error message
    $beds = []; // Set beds array to empty
}

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Room Allocation</h2>
        </div>
        <div class="card-body">
            <!-- Pagination -->
            <nav aria-label="Allocation navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1) : ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>
                    <?php
                    // Display 10 page numbers
                    $start_page = max(1, $page - 15);
                    $end_page = min($start_page + 29, $total_pages);
                    for ($i = $start_page; $i <= $end_page; $i++) : ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages) : ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addAllocationModal">
                Add Allocation
            </button>

            <!-- Modal for adding Allocation -->
            <div class="modal fade" id="addAllocationModal" tabindex="-1" role="dialog" aria-labelledby="addAllocationModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addAllocationModalLabel">Add New Allocation</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="student_id">Student</label>
                                    <select class="form-control" id="student_id" name="student_id" required>
                                        <option value="">Select Student</option>
                                        <?php foreach ($students as $student) : ?>
                                            <option value="<?php echo $student['student_id']; ?>"><?php echo $student['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="bed_id">Bed</label>
                                    <select class="form-control" id="bed_id" name="bed_id" required>
                                        <option value="">Select Bed</option>
                                        <?php foreach ($beds as $bed) : ?>
                                            <option value="<?php echo $bed['bed_id']; ?>"><?php echo "Room: " . $bed['room_number'] . " - Bed: " . $bed['bed_number']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="assignment_start_date">Start Date</label>
                                    <input type="date" class="form-control" id="assignment_start_date" name="assignment_start_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="assignment_end_date">End Date</label>
                                    <input type="date" class="form-control" id="assignment_end_date" name="assignment_end_date" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_allocation">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Display assignments -->
            <div class="row">
                <?php foreach ($assignments as $assignment) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $assignment['student_name']; ?></h5>
                                <p class="card-text">Room: <?php echo $assignment['room_number']; ?></p>
                                <p class="card-text">Bed: <?php echo $assignment['bed_number']; ?></p>
                                <p class="card-text">Start Date: <?php echo $assignment['assignment_start_date']; ?></p>
                                <p class="card-text">End Date: <?php echo $assignment['assignment_end_date']; ?></p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editAllocationModal<?php echo $assignment['assignment_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAllocationModal<?php echo $assignment['assignment_id']; ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for editing allocation -->
                    <div class="modal fade" id="editAllocationModal<?php echo $assignment['assignment_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editAllocationModalLabel<?php echo $assignment['assignment_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editAllocationModalLabel<?php echo $assignment['assignment_id']; ?>">Edit Allocation</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="student_id">Student</label>
                                            <select class="form-control" id="student_id" name="student_id" required>
                                                <option value="">Select Student</option>
                                                <?php foreach ($students as $student) : ?>
                                                    <option value="<?php echo $student['student_id']; ?>" <?php if ($student['student_id'] == $assignment['student_id']) echo 'selected'; ?>><?php echo $student['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="bed_id">Bed</label>
                                            <select class="form-control" id="bed_id" name="bed_id" required>
                                                <option value="">Select Bed</option>
                                                <?php foreach ($beds as $bed) : ?>
                                                    <option value="<?php echo $bed['bed_id']; ?>" <?php if ($bed['bed_id'] == $assignment['bed_id']) echo 'selected'; ?>><?php echo "Room: " . $bed['room_number'] . " - Bed: " . $bed['bed_number']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="assignment_start_date">Start Date</label>
                                            <input type="date" class="form-control" id="assignment_start_date" name="assignment_start_date" value="<?php echo $assignment['assignment_start_date']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="assignment_end_date">End Date</label>
                                            <input type="date" class="form-control" id="assignment_end_date" name="assignment_end_date" value="<?php echo $assignment['assignment_end_date']; ?>" required>
                                        </div>
                                        <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_allocation">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for deleting allocation -->
                    <div class="modal fade" id="deleteAllocationModal<?php echo $assignment['assignment_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteAllocationModalLabel<?php echo $assignment['assignment_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteAllocationModalLabel<?php echo $assignment['assignment_id']; ?>">Delete Allocation</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete the allocation for <?php echo $assignment['student_name']; ?>?
                                        <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger" name="delete_allocation">Delete</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>
