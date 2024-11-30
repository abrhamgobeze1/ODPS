<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as admin, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "system_admin") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Check if form is submitted for adding or editing a manager
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_manager'])) {
        // Add new manager
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
        $name = $_POST['name'];
        $contact_number = $_POST['contact_number'];

        $sql = "INSERT INTO manager (username, password, name, contact_number) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $password, $name, $contact_number);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_manager'])) {
        // Edit existing manager
        $manager_id = $_POST['manager_id'];
        $username = $_POST['username'];
        $name = $_POST['name'];
        $contact_number = $_POST['contact_number'];
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null; // Hash password if provided, otherwise keep null

        if ($password) {
            $sql = "UPDATE manager SET username = ?, password = ?, name = ?, contact_number = ? WHERE manager_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $username, $password, $name, $contact_number, $manager_id);
        } else {
            $sql = "UPDATE manager SET username = ?, name = ?, contact_number = ? WHERE manager_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $username, $name, $contact_number, $manager_id);
        }

        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_manager'])) {
        // Delete existing manager
        $manager_id = $_POST['manager_id'];

        $sql = "DELETE FROM manager WHERE manager_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $manager_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch managers
$sql_managers = "SELECT manager_id, username, name, contact_number FROM manager";
$result_managers = $conn->query($sql_managers);
$managers = $result_managers->fetch_all(MYSQLI_ASSOC);

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Managers</h2>
        </div>
        <div class="card-body">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addManagerModal">
                Add Manager
            </button>

            <!-- Modal for adding Manager -->
            <div class="modal fade" id="addManagerModal" tabindex="-1" role="dialog"
                aria-labelledby="addManagerModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addManagerModalLabel">Add New Manager</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
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
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_manager">Add Manager</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Display managers -->
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($managers as $manager): ?>
                        <tr>
                            <td><?php echo $manager['name']; ?></td>
                            <td><?php echo $manager['username']; ?></td>
                            <td><?php echo $manager['contact_number']; ?></td>
                            <td>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#editManagerModal<?php echo $manager['manager_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#deleteManagerModal<?php echo $manager['manager_id']; ?>">
                                    Delete
                                </button>
                            </td>
                        </tr>

                        <!-- Modal for editing manager -->
                        <div class="modal fade" id="editManagerModal<?php echo $manager['manager_id']; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="editManagerModalLabel<?php echo $manager['manager_id']; ?>"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="editManagerModalLabel<?php echo $manager['manager_id']; ?>">Edit Manager
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="manager_id"
                                                value="<?php echo $manager['manager_id']; ?>">
                                            <div class="form-group">
                                                <label for="username">Username</label>
                                                <input type="text" class="form-control" id="username" name="username"
                                                    value="<?php echo $manager['username']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input type="password" class="form-control" id="password" name="password">
                                            </div>
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="<?php echo $manager['name']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="contact_number">Contact Number</label>
                                                <input type="text" class="form-control" id="contact_number"
                                                    name="contact_number" value="<?php echo $manager['contact_number']; ?>"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" name="edit_manager">Save
                                                Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for deleting manager -->
                        <div class="modal fade" id="deleteManagerModal<?php echo $manager['manager_id']; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="deleteManagerModalLabel<?php echo $manager['manager_id']; ?>"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="deleteManagerModalLabel<?php echo $manager['manager_id']; ?>">Delete
                                                Manager</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete the manager
                                                "<?php echo $manager['name']; ?>"?</p>
                                            <input type="hidden" name="manager_id"
                                                value="<?php echo $manager['manager_id']; ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger"
                                                name="delete_manager">Delete</button>
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