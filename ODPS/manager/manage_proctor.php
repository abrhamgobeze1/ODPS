<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as manager, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "manager") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Check if form is submitted for adding or editing a proctor
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_proctor'])) {
        // Add new proctor
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
        $name = $_POST['name'];
        $contact_number = $_POST['contact_number'];
        $block_id = $_POST['block_id'];

        $sql = "INSERT INTO proctor (username, password, name, contact_number, block_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $password, $name, $contact_number, $block_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_proctor'])) {
        // Edit existing proctor
        $proctor_id = $_POST['proctor_id'];
        $username = $_POST['username'];
        $name = $_POST['name'];
        $contact_number = $_POST['contact_number'];
        $block_id = $_POST['block_id'];
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null; // Hash password if provided, otherwise keep null

        if ($password) {
            $sql = "UPDATE proctor SET username = ?, password = ?, name = ?, contact_number = ?, block_id = ? WHERE proctor_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssii", $username, $password, $name, $contact_number, $block_id, $proctor_id);
        } else {
            $sql = "UPDATE proctor SET username = ?, name = ?, contact_number = ?, block_id = ? WHERE proctor_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiii", $username, $name, $contact_number, $block_id, $proctor_id);
        }

        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_proctor'])) {
        // Delete existing proctor
        $proctor_id = $_POST['proctor_id'];

        $sql = "DELETE FROM proctor WHERE proctor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $proctor_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch proctors for the manager's assigned dormitory
// Fetch proctors for the manager's assigned dormitory
// Fetch proctors for the manager's assigned dormitory
$sql_proctors = "SELECT proctor_id, username, name, contact_number, proctor.block_id FROM proctor
                INNER JOIN blocks ON proctor.block_id = blocks.block_id
                INNER JOIN dormitories ON blocks.dormitory_id = dormitories.dormitory_id
                WHERE dormitories.dormitory_id = ?";

$stmt_proctors = $conn->prepare($sql_proctors);
if (!$stmt_proctors) {
    die("Error preparing statement: " . $conn->error);
}
$stmt_proctors->bind_param("i", $_SESSION["dormitory_id"]);
$result_proctors = $stmt_proctors->execute();
if (!$result_proctors) {
    die("Error executing statement: " . $stmt_proctors->error);
}
$result_proctors = $stmt_proctors->get_result();
$proctors = $result_proctors->fetch_all(MYSQLI_ASSOC);
$stmt_proctors->close();


// Fetch blocks for the manager's assigned dormitory
$sql_blocks = "SELECT block_id, block_name FROM blocks WHERE dormitory_id = ?";
$stmt_blocks = $conn->prepare($sql_blocks);
$stmt_blocks->bind_param("i", $_SESSION["dormitory_id"]);
$stmt_blocks->execute();
$result_blocks = $stmt_blocks->get_result();
$blocks = $result_blocks->fetch_all(MYSQLI_ASSOC);
$stmt_blocks->close();

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Proctors</h2>
        </div>
        <div class="card-body">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addProctorModal">
                Add Proctor
            </button>

            <!-- Modal for adding Proctor -->
            <div class="modal fade" id="addProctorModal" tabindex="-1" role="dialog" aria-labelledby="addProctorModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addProctorModalLabel">Add New Proctor</h5>
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
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                                </div>
                                <div class="form-group">
                                    <label for="block_id">Block</label>
                                    <select class="form-control" id="block_id" name="block_id" required>
                                        <option value="">Select Block</option>
                                        <?php foreach ($blocks as $block): ?>
                                            <option value="<?php echo $block['block_id']; ?>"><?php echo $block['block_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_proctor">Add Proctor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Display proctors -->
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Contact Number</th>
                        <th>Block</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proctors as $proctor): ?>
                        <tr>
                            <td><?php echo $proctor['name']; ?></td>
                            <td><?php echo $proctor['username']; ?></td>
                            <td><?php echo $proctor['contact_number']; ?></td>
                            <td><?php echo $proctor['block_id']; ?></td>
                            <td>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#editProctorModal<?php echo $proctor['proctor_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#deleteProctorModal<?php echo $proctor['proctor_id']; ?>">
                                    Delete
                                </button>
                            </td>
                        </tr>

                        <!-- Modal for editing proctor -->
                        <div class="modal fade" id="editProctorModal<?php echo $proctor['proctor_id']; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="editProctorModalLabel<?php echo $proctor['proctor_id']; ?>"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="editProctorModalLabel<?php echo $proctor['proctor_id']; ?>">Edit Proctor
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="proctor_id"
                                                value="<?php echo $proctor['proctor_id']; ?>">
                                            <div class="form-group">
                                                <label for="username">Username</label>
                                                <input type="text" class="form-control" id="username" name="username"
                                                    value="<?php echo $proctor['username']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input type="password" class="form-control" id="password" name="password">
                                            </div>
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="<?php echo $proctor['name']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="contact_number">Contact Number</label>
                                                <input type="text" class="form-control" id="contact_number"
                                                    name="contact_number" value="<?php echo $proctor['contact_number']; ?>"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="block_id">Block</label>
                                                <select class="form-control" id="block_id" name="block_id" required>
                                                    <option value="">Select Block</option>
                                                    <?php foreach ($blocks as $block): ?>
                                                        <option value="<?php echo $block['block_id']; ?>"
                                                            <?php echo ($proctor['block_id'] == $block['block_id']) ? 'selected' : ''; ?>>
                                                            <?php echo $block['block_name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" name="edit_proctor">Save
                                                Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for deleting proctor -->
                        <div class="modal fade" id="deleteProctorModal<?php echo $proctor['proctor_id']; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="deleteProctorModalLabel<?php echo $proctor['proctor_id']; ?>"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="deleteProctorModalLabel<?php echo $proctor['proctor_id']; ?>">Delete
                                                Proctor</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete the proctor
                                                "<?php echo $proctor['name']; ?>"?</p>
                                            <input type="hidden" name="proctor_id"
                                                value="<?php echo $proctor['proctor_id']; ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger"
                                                name="delete_proctor">Delete</button>
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
