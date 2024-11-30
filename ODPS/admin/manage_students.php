<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as admin, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Pagination variables
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Check if form is submitted for adding or editing a student
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_student'])) {
        // Add new student
        $department_id = $_POST['department_id'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
        $name = $_POST['name'];
        $contact_number = $_POST['contact_number'];
        $gender = $_POST['gender'];
        $batch = $_POST['batch'];

        $sql = "INSERT INTO student (department_id, username, password, name, contact_number, gender,batch) VALUES (?, ?, ?, ?, ?,?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $department_id, $username, $password, $name, $contact_number, $gender,$batch);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_student'])) {
        // Edit existing student
        $student_id = $_POST['student_id'];
        $department_id = $_POST['department_id'];
        $username = $_POST['username'];
        $name = $_POST['name'];
        $contact_number = $_POST['contact_number'];
        $gender = $_POST['gender'];
        $batch = $_POST['batch'];

        // Check if password is provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
            $sql = "UPDATE student SET department_id = ?, username = ?, name = ?, contact_number = ?, gender = ?,batch = ?, password = ? WHERE student_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssssi", $department_id, $username, $name, $contact_number, $gender, $batch, $password, $student_id);
        } else {
            $sql = "UPDATE student SET department_id = ?, username = ?, name = ?, contact_number = ?, gender = ?, batch = ? WHERE student_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssi", $department_id, $username, $name, $contact_number, $gender, $batch, $student_id);
        }

        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_student'])) {
        // Delete existing student
        $student_id = $_POST['student_id'];

        $sql = "DELETE FROM student WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch departments
$sql_departments = "SELECT department_id, department_name FROM departments ORDER BY department_name";
$result_departments = $conn->query($sql_departments);
$departments = $result_departments->fetch_all(MYSQLI_ASSOC);

// Fetch total number of students
$sql_count_students = "SELECT COUNT(*) AS total_students FROM student";
$result_count_students = $conn->query($sql_count_students);
$total_students = $result_count_students->fetch_assoc()['total_students'];

// Fetch students for the current page
$search = isset($_GET['search']) ? $_GET['search'] : '';
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : '';

$sql_students = "SELECT student_id, department_id, username, name, contact_number, gender,batch FROM student";

// Applying search filters
if (!empty($search)) {
    $sql_students .= " WHERE username LIKE '%$search%' OR name LIKE '%$search%' ";
}

// Applying order by
if (!empty($order_by)) {
    $sql_students .= " ORDER BY $order_by";
}

$sql_students .= " LIMIT ?, ?";
$stmt_students = $conn->prepare($sql_students);
$stmt_students->bind_param("ii", $offset, $items_per_page);
$stmt_students->execute();
$result_students = $stmt_students->get_result();
$students = $result_students->fetch_all(MYSQLI_ASSOC);
$stmt_students->close();

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Students</h2>
        </div>
        <div class="card-body">
            <!-- Search and Order by form -->
            <form class="form-inline mb-3">
                <div class="form-group mr-2">
                    <label for="search" class="mr-2">Search:</label>
                    <input type="text" class="form-control" id="search" name="search" value="<?php echo $search; ?>">
                </div>
                <div class="form-group mr-2">
                    <label for="order_by" class="mr-2">Order By:</label>
                    <select class="form-control" id="order_by" name="order_by">
                        <option value="">Select</option>
                        <option value="username" <?php echo ($order_by == 'username') ? 'selected' : ''; ?>>Username
                        </option>
                        <option value="name" <?php echo ($order_by == 'name') ? 'selected' : ''; ?>>Name</option>
                        <option value="department_id" <?php echo ($order_by == 'department_id') ? 'selected' : ''; ?>>
                            Department</option>
                        <option value="gender" <?php echo ($order_by == 'gender') ? 'selected' : ''; ?>>Gender</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Apply</button>
            </form>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addStudentModal">
                Add Student
            </button>

            <!-- Modal for adding Student -->
            <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog"
                aria-labelledby="addStudentModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="department_id">Department</label>
                                    <select class="form-control" id="department_id" name="department_id" required>
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $department): ?>
                                            <option value="<?php echo $department['department_id']; ?>">
                                                <?php echo $department['department_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="batch">Batch</label>
                                    <select class="form-control" id="batch" name="batch" required>
                                        <option value="">Select Batch</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_student">Add Student</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Pagination -->
            <?php if ($total_students > $items_per_page): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php
                        $total_pages = ceil($total_students / $items_per_page);
                        $start_page = max($page - 15, 1);
                        $end_page = min($start_page + 29, $total_pages);
                        ?>
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
            <!-- Display students -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Department</th>
                        <th>Batch</th>
                        <th>Contact Number</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['student_id']; ?></td>
                            <td><?php echo $student['name']; ?></td>
                            <td><?php echo $student['username']; ?></td>
                            <td><?php echo getDepartmentName($student['department_id'], $departments); ?></td>
                            <td><?php echo $student['batch']; ?></td>

                            <td><?php echo $student['contact_number']; ?></td>
                            <td><?php echo $student['gender']; ?></td>
                            <td>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#editStudentModal<?php echo $student['student_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#deleteStudentModal<?php echo $student['student_id']; ?>">
                                    Delete
                                </button>
                            </td>
                        </tr>

                        <!-- Modal for editing student -->
                        <div class="modal fade" id="editStudentModal<?php echo $student['student_id']; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="editStudentModalLabel<?php echo $student['student_id']; ?>"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="editStudentModalLabel<?php echo $student['student_id']; ?>">Edit Student
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="student_id"
                                                value="<?php echo $student['student_id']; ?>">
                                            <div class="form-group">
                                                <label for="department_id">Department</label>
                                                <select class="form-control" id="department_id" name="department_id"
                                                    required>
                                                    <option value="">Select Department</option>
                                                    <?php foreach ($departments as $department): ?>
                                                        <option value="<?php echo $department['department_id']; ?>" <?php if ($department['department_id'] == $student['department_id'])
                                                               echo 'selected'; ?>><?php echo $department['department_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="username">Username</label>
                                                <input type="text" class="form-control" id="username" name="username"
                                                    value="<?php echo $student['username']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input type="password" class="form-control" id="password" name="password"
                                                    value="">
                                            </div>
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="<?php echo $student['name']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="contact_number">Contact Number</label>
                                                <input type="text" class="form-control" id="contact_number"
                                                    name="contact_number" value="<?php echo $student['contact_number']; ?>"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="gender">Gender</label>
                                                <select class="form-control" id="gender" name="gender" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="Male" <?php if ($student['gender'] == 'Male')
                                                        echo 'selected'; ?>>Male</option>
                                                    <option value="Female" <?php if ($student['gender'] == 'Female')
                                                        echo 'selected'; ?>>Female</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="batch">batch</label>
                                                <select class="form-control" id="batch" name="batch" required>
                                                    <option value="">Select Batch</option>
                                                    <option value="1" <?php if ($student['batch'] == '1')
                                                        echo 'selected'; ?>>1</option>
                                                    <option value="3" <?php if ($student['batch'] == '2')
                                                        echo 'selected'; ?>>2</option>
                                                    <option value="3" <?php if ($student['batch'] == '3')
                                                        echo 'selected'; ?>>3</option>
                                                    <option value="4" <?php if ($student['batch'] == '4')
                                                        echo 'selected'; ?>>4</option>

                                                </select>
                                            </div>







                                   
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" name="edit_student">Save
                                                Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>



                        <!-- Modal for deleting student -->
                        <div class="modal fade" id="deleteStudentModal<?php echo $student['student_id']; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="deleteStudentModalLabel<?php echo $student['student_id']; ?>"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="deleteStudentModalLabel<?php echo $student['student_id']; ?>">Delete
                                                Student
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete the student
                                                "<?php echo $student['name']; ?>"?
                                            </p>
                                            <input type="hidden" name="student_id"
                                                value="<?php echo $student['student_id']; ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger"
                                                name="delete_student">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>

<?php
// Function to get department name by ID
function getDepartmentName($department_id, $departments)
{
    foreach ($departments as $department) {
        if ($department['department_id'] == $department_id) {
            return $department['department_name'];
        }
    }
    return "N/A";
}
?>