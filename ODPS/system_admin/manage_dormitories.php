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

// Check if form is submitted for adding or editing a dormitory
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_dormitory'])) {
        // Add new dormitory
        $dormitory_name = $_POST['dormitory_name'];
        $dormitory_type = $_POST['dormitory_type'];
        $dormitory_location = $_POST['dormitory_location'];
        $dormitory_capacity = $_POST['dormitory_capacity'];

        $sql = "INSERT INTO dormitories (dormitory_name, dormitory_type, dormitory_location, dormitory_capacity) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $dormitory_name, $dormitory_type, $dormitory_location, $dormitory_capacity);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_dormitory'])) {
        // Edit existing dormitory
        $dormitory_name = $_POST['dormitory_name'];
        $dormitory_type = $_POST['dormitory_type'];
        $dormitory_location = $_POST['dormitory_location'];
        $dormitory_capacity = $_POST['dormitory_capacity'];
        $dormitory_id = $_POST['dormitory_id'];

        $sql = "UPDATE dormitories SET dormitory_name = ?, dormitory_type = ?, dormitory_location = ?, dormitory_capacity = ? WHERE dormitory_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $dormitory_name, $dormitory_type, $dormitory_location, $dormitory_capacity, $dormitory_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_dormitory'])) {
        // Delete existing dormitory
        $dormitory_id = $_POST['dormitory_id'];

        $sql = "DELETE FROM dormitories WHERE dormitory_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $dormitory_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch dormitories
$sql = "SELECT dormitory_id, dormitory_name, dormitory_type, dormitory_location, dormitory_capacity FROM dormitories";
$result = $conn->query($sql);
$dormitories = $result->fetch_all(MYSQLI_ASSOC);

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Dormitories</h2>
        </div>
        <div class="card-body">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addDormitoryModal">
                Add Dormitory
            </button>

            <!-- Modal for adding Dormitory -->
            <div class="modal fade" id="addDormitoryModal" tabindex="-1" role="dialog"
                aria-labelledby="addDormitoryModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addDormitoryModalLabel">Add New Dormitory</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="dormitory_name">Dormitory Name</label>
                                    <input type="text" class="form-control" id="dormitory_name" name="dormitory_name"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="dormitory_type">Dormitory Type</label>
                                    <select class="form-control" id="dormitory_type" name="dormitory_type" required>
                                        <option value="">Select Type</option>
                                        <option value="FBE Male">FBE Male</option>
                                        <option value="FBE Female">FBE Female</option>
                                        <option value="Main Male">Main Male</option>
                                        <option value="Main Female">Main Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="dormitory_location">Location</label>
                                    <input type="text" class="form-control" id="dormitory_location"
                                        name="dormitory_location" required>
                                </div>
                                <div class="form-group">
                                    <label for="dormitory_capacity">Capacity</label>
                                    <input type="number" class="form-control" id="dormitory_capacity"
                                        name="dormitory_capacity" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_dormitory">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Display dormitories -->
            <div class="row">
                <?php foreach ($dormitories as $dormitory): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $dormitory['dormitory_name']; ?></h5>
                                <p class="card-text">Type: <?php echo $dormitory['dormitory_type']; ?></p>
                                <p class="card-text">Location: <?php echo $dormitory['dormitory_location']; ?></p>
                                <p class="card-text">Capacity: <?php echo $dormitory['dormitory_capacity']; ?></p>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#editDormitoryModal<?php echo $dormitory['dormitory_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#deleteDormitoryModal<?php echo $dormitory['dormitory_id']; ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for editing dormitory -->
                    <div class="modal fade" id="editDormitoryModal<?php echo $dormitory['dormitory_id']; ?>" tabindex="-1"
                        role="dialog" aria-labelledby="editDormitoryModalLabel<?php echo $dormitory['dormitory_id']; ?>"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="editDormitoryModalLabel<?php echo $dormitory['dormitory_id']; ?>">Edit
                                            Dormitory</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="dormitory_name">Dormitory Name</label>
                                            <input type="text" class="form-control" id="dormitory_name"
                                                name="dormitory_name" value="<?php echo $dormitory['dormitory_name']; ?>"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="dormitory_type">Dormitory Type</label>
                                            <select class="form-control" id="dormitory_type" name="dormitory_type" required>
                                                <option value="">Select Type</option>
                                                <option value="FBE Male" <?php if ($dormitory['dormitory_type'] == 'FBE Male')
                                                    echo 'selected'; ?>>FBE Male</option>
                                                <option value="FBE Female" <?php if ($dormitory['dormitory_type'] == 'FBE Female')
                                                    echo 'selected'; ?>>FBE Female</option>
                                                <option value="Main Male" <?php if ($dormitory['dormitory_type'] == 'Main Male')
                                                    echo 'selected'; ?>>Main Male</option>
                                                <option value="Main Female" <?php if ($dormitory['dormitory_type'] == 'Main Female')
                                                    echo 'selected'; ?>>Main Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="dormitory_location">Location</label>
                                            <input type="text" class="form-control" id="dormitory_location"
                                                name="dormitory_location"
                                                value="<?php echo $dormitory['dormitory_location']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="dormitory_capacity">Capacity</label>
                                            <input type="number" class="form-control" id="dormitory_capacity"
                                                name="dormitory_capacity"
                                                value="<?php echo $dormitory['dormitory_capacity']; ?>" required>
                                        </div>
                                        <input type="hidden" name="dormitory_id"
                                            value="<?php echo $dormitory['dormitory_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_dormitory">Save
                                            changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Modal for deleting dormitory -->
                    <div class="modal fade" id="deleteDormitoryModal<?php echo $dormitory['dormitory_id']; ?>" tabindex="-1"
                        role="dialog" aria-labelledby="deleteDormitoryModalLabel<?php echo $dormitory['dormitory_id']; ?>"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="deleteDormitoryModalLabel<?php echo $dormitory['dormitory_id']; ?>">Delete
                                            Dormitory</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete the dormitory
                                        "<?php echo $dormitory['dormitory_name']; ?>"?
                                        <input type="hidden" name="dormitory_id"
                                            value="<?php echo $dormitory['dormitory_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger" name="delete_dormitory">Delete</button>
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