<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as a manager, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "manager") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Pagination
$limit = 30; // Number of rooms per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Check if form is submitted for adding or editing a room
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_room'])) {
        // Add new room
        $room_number = $_POST['room_number'];
        $block_id = $_POST['block_id']; // Get block ID from form
        $room_description = $_POST['room_description'];
        $room_facilities = $_POST['room_facilities'];
        $room_capacity = $_POST['room_capacity'];
        $room_availability = isset($_POST['room_availability']) ? 1 : 0;

        $sql = "INSERT INTO rooms (room_number, block_id, room_description, room_facilities, room_capacity, room_availability) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisssi", $room_number, $block_id, $room_description, $room_facilities, $room_capacity, $room_availability);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_room'])) {
        // Edit existing room
        $room_number = $_POST['room_number'];
        $block_id = $_POST['block_id']; // Get block ID from form
        $room_description = $_POST['room_description'];
        $room_facilities = $_POST['room_facilities'];
        $room_capacity = $_POST['room_capacity'];
        $room_availability = isset($_POST['room_availability']) ? 1 : 0;
        $room_id = $_POST['room_id'];

        $sql = "UPDATE rooms SET room_number = ?, block_id = ?, room_description = ?, room_facilities = ?, room_capacity = ?, room_availability = ? WHERE room_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisssii", $room_number, $block_id, $room_description, $room_facilities, $room_capacity, $room_availability, $room_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_room'])) {
        // Delete existing room
        $room_id = $_POST['room_id'];

        $sql = "DELETE FROM rooms WHERE room_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $stmt->close();
    }
    // Redirect to avoid resubmission on page refresh
    header("Location: $_SERVER[PHP_SELF]?page=$page");
    exit;
}

// Fetch rooms with pagination for the manager's dormitory only
$sql_rooms = "SELECT r.room_id, r.room_number, r.room_description, r.room_facilities, r.room_capacity, r.room_availability, b.block_name 
              FROM rooms r 
              INNER JOIN blocks b ON r.block_id = b.block_id 
              INNER JOIN dormitories d ON b.dormitory_id = d.dormitory_id 
              INNER JOIN manager m ON d.dormitory_id = m.dormitory_id 
              WHERE m.manager_id = ? 
              LIMIT ?, ?";
$stmt = $conn->prepare($sql_rooms);
$stmt->bind_param("iii", $_SESSION["user_id"], $start, $limit);
$stmt->execute();
$result_rooms = $stmt->get_result();
$rooms = $result_rooms->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count total number of rooms
$sql_count = "SELECT COUNT(*) AS total 
              FROM rooms r 
              INNER JOIN blocks b ON r.block_id = b.block_id 
              INNER JOIN dormitories d ON b.dormitory_id = d.dormitory_id 
              INNER JOIN manager m ON d.dormitory_id = m.dormitory_id 
              WHERE m.manager_id = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $_SESSION["user_id"]);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_rooms = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_rooms / $limit);

// Fetch blocks for the manager's dormitory only
$sql_blocks = "SELECT * 
               FROM blocks b 
               INNER JOIN dormitories d ON b.dormitory_id = d.dormitory_id 
               INNER JOIN manager m ON d.dormitory_id = m.dormitory_id 
               WHERE m.manager_id = ?";
$stmt_blocks = $conn->prepare($sql_blocks);
$stmt_blocks->bind_param("i", $_SESSION["user_id"]);
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
            <h2 class="mb-0">Manage Rooms</h2>
        </div>
        <div class="card-body">
            <!-- Pagination -->
            <nav aria-label="Room navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>
                    <?php
                    // Display 10 page numbers
                    $start_page = max(1, $page - 10);
                    $end_page = min($start_page + 19, $total_pages);
                    for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?php if ($i == $page)
                            echo 'active'; ?>"><a class="page-link"
                                href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addRoomModal">
                Add Room
            </button>

            <!-- Modal for adding Room -->
            <div class="modal fade" id="addRoomModal" tabindex="-1" role="dialog" aria-labelledby="addRoomModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addRoomModalLabel">Add New Room</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="room_number">Room Number</label>
                                    <input type="text" class="form-control" id="room_number" name="room_number"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="block_id">Block</label>
                                    <select class="form-control" id="block_id" name="block_id" required>
                                        <option value="">Select Block</option>
                                        <?php foreach ($blocks as $block): ?>
                                            <option value="<?php echo $block['block_id']; ?>">
                                                <?php echo $block['block_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="room_description">Description</label>
                                    <textarea class="form-control" id="room_description" name="room_description"
                                        rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="room_facilities">Facilities</label>
                                    <textarea class="form-control" id="room_facilities" name="room_facilities"
                                        rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="room_capacity">Capacity</label>
                                    <input type="number" class="form-control" id="room_capacity" name="room_capacity"
                                        required>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="room_availability"
                                        name="room_availability" value="1">
                                    <label class="form-check-label" for="room_availability">Available</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_room">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Display rooms -->
            <div class="row">
                <?php foreach ($rooms as $room): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $room['room_number']; ?></h5>
                                <p class="card-text">Block: <?php echo $room['block_name']; ?></p>
                                <p class="card-text">Description: <?php echo $room['room_description']; ?></p>
                                <p class="card-text">Facilities: <?php echo $room['room_facilities']; ?></p>
                                <p class="card-text">Capacity: <?php echo $room['room_capacity']; ?></p>
                                <p class="card-text">Availability:
                                    <?php echo $room['room_availability'] ? 'Available' : 'Not Available'; ?></p>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#editRoomModal<?php echo $room['room_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#deleteRoomModal<?php echo $room['room_id']; ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Modal for editing room -->
                    <div class="modal fade" id="editRoomModal<?php echo $room['room_id']; ?>" tabindex="-1" role="dialog"
                        aria-labelledby="editRoomModalLabel<?php echo $room['room_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editRoomModalLabel<?php echo $room['room_id']; ?>">Edit
                                            Room</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="room_number">Room Number</label>
                                            <input type="text" class="form-control" id="room_number" name="room_number"
                                                value="<?php echo $room['room_number']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="block_id">Block</label>
                                            <select class="form-control" id="block_id" name="block_id" required>
                                                <option value="">Select Block</option>
                                                <?php foreach ($blocks as $block): ?>
                                                    <option value="<?php echo $block['block_id']; ?>">
                                                        <?php echo $block['block_name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="room_description">Description</label>
                                            <textarea class="form-control" id="room_description" name="room_description"
                                                rows="3"><?php echo $room['room_description']; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="room_facilities">Facilities</label>
                                            <textarea class="form-control" id="room_facilities" name="room_facilities"
                                                rows="3"><?php echo $room['room_facilities']; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="room_capacity">Capacity</label>
                                            <input type="number" class="form-control" id="room_capacity"
                                                name="room_capacity" value="<?php echo $room['room_capacity']; ?>" required>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="room_availability"
                                                name="room_availability" value="1" <?php if ($room['room_availability'])
                                                    echo 'checked'; ?>>
                                            <label class="form-check-label" for="room_availability">Available</label>
                                        </div>
                                        <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_room">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                   <!-- Modal for deleting room -->
                   <div class="modal fade" id="deleteRoomModal<?php echo $room['room_id']; ?>" tabindex="-1" role="dialog"
                        aria-labelledby="deleteRoomModalLabel<?php echo $room['room_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteRoomModalLabel<?php echo $room['room_id']; ?>">
                                            Delete Room</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete room <?php echo $room['room_number']; ?>?
                                        <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger" name="delete_room">Delete</button>
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