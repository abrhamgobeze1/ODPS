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

// Check if form is submitted for adding or editing a department
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_department'])) {
        // Add new department
        $department_name = $_POST['department_name'];
        $college_id = $_POST['college_id']; // Get college ID from form

        $sql = "INSERT INTO departments (department_name, college_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $department_name, $college_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_department'])) {
        // Edit existing department
        $department_name = $_POST['department_name'];
        $department_id = $_POST['department_id'];
        $college_id = $_POST['college_id']; // Get college ID from form

        $sql = "UPDATE departments SET department_name = ?, college_id = ? WHERE department_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $department_name, $college_id, $department_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_department'])) {
        // Delete existing department
        $department_id = $_POST['department_id'];

        $sql = "DELETE FROM departments WHERE department_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $department_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch colleges
$sql_colleges = "SELECT college_id, college_name FROM colleges";
$result_colleges = $conn->query($sql_colleges);
$colleges = $result_colleges->fetch_all(MYSQLI_ASSOC);

// Fetch departments if a college is selected
$departments = [];
$selected_college_id = null;
if (isset($_POST['college_id'])) {
    $selected_college_id = $_POST['college_id'];
    $sql_departments = "SELECT department_id, department_name, college_id FROM departments WHERE college_id = $selected_college_id";
    $result_departments = $conn->query($sql_departments);
    $departments = $result_departments->fetch_all(MYSQLI_ASSOC);
}

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <div class="row">


        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="list-group">
                <section class="dashboard card">
                    <div class="card-header">
                        <a href="manage_departments.php" class="list-group-item ">
                            <h3> Sellect Colleges </h3>
                        </a>
                    </div>
                    <?php foreach ($colleges as $college) : ?>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="college_id" value="<?php echo $college['college_id']; ?>">
                            <button type="submit" class="list-group-item <?php echo ($college['college_id'] == $selected_college_id) ? 'active' : ''; ?>">
                                <?php echo $college['college_name']; ?>
                            </button>
                        </form>
                    <?php endforeach; ?>
                </section>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <section class="dashboard card">
                <div class="card-header">
                    <h2 class="mb-0">Manage Departments</h2>
                </div>
                <div class="card-body">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addDepartmentModal">
                        Add Department
                    </button>

                    <!-- Modal for adding Department -->
                    <div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addDepartmentModalLabel">Add New Department</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="department_name">Department Name</label>
                                            <input type="text" class="form-control" id="department_name" name="department_name" required>
                                        </div>
                                        <input type="hidden" name="college_id" value="<?php echo $selected_college_id; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="add_department">Save
                                            changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Display departments -->
                    <div class="row">
                        <?php foreach ($departments as $department) : ?>
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $department['department_name']; ?></h5>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editDepartmentModal<?php echo $department['department_id']; ?>">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteDepartmentModal<?php echo $department['department_id']; ?>">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal for editing department -->
                            <div class="modal fade" id="editDepartmentModal<?php echo $department['department_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editDepartmentModalLabel<?php echo $department['department_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editDepartmentModalLabel<?php echo $department['department_id']; ?>">
                                                    Edit Department</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="department_name">Department Name</label>
                                                    <input type="text" class="form-control" id="department_name" name="department_name" value="<?php echo $department['department_name']; ?>" required>
                                                </div>
                                                <input type="hidden" name="college_id" value="<?php echo $selected_college_id; ?>">
                                                <input type="hidden" name="department_id" value="<?php echo $department['department_id']; ?>">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" name="edit_department">Save
                                                    changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal for deleting department -->
                            <div class="modal fade" id="deleteDepartmentModal<?php echo $department['department_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteDepartmentModalLabel<?php echo $department['department_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteDepartmentModalLabel<?php echo $department['department_id']; ?>">
                                                    Delete Department</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete the department
                                                "<?php echo $department['department_name']; ?>"?
                                                <input type="hidden" name="department_id" value="<?php echo $department['department_id']; ?>">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger" name="delete_department">Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>

<?php
// Function to get college name by ID
function getCollegeName($college_id, $colleges)
{
    foreach ($colleges as $college) {
        if ($college['college_id'] == $college_id) {
            return $college['college_name'];
        }
    }
    return "N/A";
}
?>