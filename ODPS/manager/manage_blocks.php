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

// Check if form is submitted for adding or editing a block
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_block'])) {
        // Add new block
        $block_name = $_POST['block_name'];
        $block_capacity = $_POST['block_capacity'];
        $dormitory_id = $_POST['dormitory_id']; // Get dormitory ID from form

        $sql = "INSERT INTO blocks (block_name, block_capacity, dormitory_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $block_name, $block_capacity, $dormitory_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_block'])) {
        // Edit existing block
        $block_name = $_POST['block_name'];
        $block_capacity = $_POST['block_capacity'];
        $block_id = $_POST['block_id'];
        $dormitory_id = $_POST['dormitory_id']; // Get dormitory ID from form

        $sql = "UPDATE blocks SET block_name = ?, block_capacity = ?, dormitory_id = ? WHERE block_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siii", $block_name, $block_capacity, $dormitory_id, $block_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_block'])) {
        // Delete existing block
        $block_id = $_POST['block_id'];

        $sql = "DELETE FROM blocks WHERE block_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $block_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch blocks and dormitories assigned to the manager
$sql_blocks = "SELECT blocks.block_id, blocks.block_name, blocks.block_capacity, blocks.dormitory_id 
                FROM blocks 
                INNER JOIN dormitories ON blocks.dormitory_id = dormitories.dormitory_id 
                INNER JOIN manager ON dormitories.dormitory_id = manager.dormitory_id 
                WHERE manager.username = ?";
$stmt_blocks = $conn->prepare($sql_blocks);
$stmt_blocks->bind_param("s", $_SESSION["username"]);
$stmt_blocks->execute();
$result_blocks = $stmt_blocks->get_result();
$blocks = $result_blocks->fetch_all(MYSQLI_ASSOC);
$stmt_blocks->close();
// Fetch dormitory assigned to the manager
$sql_dormitory = "SELECT dormitories.dormitory_id, dormitories.dormitory_name FROM dormitories 
                  INNER JOIN manager ON dormitories.dormitory_id = manager.dormitory_id 
                  WHERE manager.username = ?";
$stmt_dormitory = $conn->prepare($sql_dormitory);
if (!$stmt_dormitory) {
    echo "Error preparing statement: " . $conn->error;
    exit;
}
$stmt_dormitory->bind_param("s", $_SESSION["username"]);
$stmt_dormitory->execute();
$result_dormitory = $stmt_dormitory->get_result();
if (!$result_dormitory) {
    echo "Error fetching dormitories: " . $conn->error;
    exit;
}
$dormitories = $result_dormitory->fetch_all(MYSQLI_ASSOC);
$stmt_dormitory->close();


// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Blocks</h2>
        </div>
        <div class="card-body">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addBlockModal">
                Add Block
            </button>

            <!-- Modal for adding Block -->
            <div class="modal fade" id="addBlockModal" tabindex="-1" role="dialog" aria-labelledby="addBlockModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addBlockModalLabel">Add New Block</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="block_name">Block Name</label>
                                    <input type="text" class="form-control" id="block_name" name="block_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="block_capacity">Capacity</label>
                                    <input type="number" class="form-control" id="block_capacity" name="block_capacity" required>
                                </div>
                                <div class="form-group">
                                    <label for="dormitory_id">Dormitory</label>
                                    <select class="form-control" id="dormitory_id" name="dormitory_id" required>
                                        <option value="">Select Dormitory</option>
                                        <?php foreach ($dormitories as $dormitory) : ?>
                                            <option value="<?php echo $dormitory['dormitory_id']; ?>"><?php echo $dormitory['dormitory_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_block">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Display blocks -->
            <div class="row">
                <?php foreach ($blocks as $block) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $block['block_name']; ?></h5>
                                <p class="card-text">Capacity: <?php echo $block['block_capacity']; ?></p>
                                <p class="card-text">Dormitory: <?php echo getDormitoryName($block['dormitory_id'], $dormitories); ?></p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editBlockModal<?php echo $block['block_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteBlockModal<?php echo $block['block_id']; ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for editing block -->
                    <div class="modal fade" id="editBlockModal<?php echo $block['block_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editBlockModalLabel<?php echo $block['block_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editBlockModalLabel<?php echo $block['block_id']; ?>">Edit Block</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="block_name">Block Name</label>
                                            <input type="text" class="form-control" id="block_name" name="block_name" value="<?php echo $block['block_name']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="block_capacity">Capacity</label>
                                            <input type="number" class="form-control" id="block_capacity" name="block_capacity" value="<?php echo $block['block_capacity']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="dormitory_id">Dormitory</label>
                                            <select class="form-control" id="dormitory_id" name="dormitory_id" required>
                                                <option value="">Select Dormitory</option>
                                                <?php foreach ($dormitories as $dormitory) : ?>
                                                    <option value="<?php echo $dormitory['dormitory_id']; ?>" <?php if ($dormitory['dormitory_id'] == $block['dormitory_id']) echo 'selected'; ?>><?php echo $dormitory['dormitory_name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <input type="hidden" name="block_id" value="<?php echo $block['block_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_block">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for deleting block -->
                    <div class="modal fade" id="deleteBlockModal<?php echo $block['block_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteBlockModalLabel<?php echo $block['block_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteBlockModalLabel<?php echo $block['block_id']; ?>">Delete Block</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete the block "<?php echo $block['block_name']; ?>"?
                                        <input type="hidden" name="block_id" value="<?php echo $block['block_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger" name="delete_block">Delete</button>
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

<?php
// Function to get dormitory name by ID
function getDormitoryName($dormitory_id, $dormitories)
{
    foreach ($dormitories as $dormitory) {
        if ($dormitory['dormitory_id'] == $dormitory_id) {
            return $dormitory['dormitory_name'];
        }
    }
    return "N/A";
}
?>
