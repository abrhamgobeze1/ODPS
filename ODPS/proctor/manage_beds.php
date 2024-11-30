<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as proctor, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "proctor") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Pagination
$limit = 30; // Number of beds per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Check if form is submitted for adding or editing a bed
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_bed'])) {
        // Add new bed
        $room_id = $_POST['room_id'];
        $bed_number = $_POST['bed_number'];

        $sql = "INSERT INTO beds (room_id, bed_number) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $room_id, $bed_number);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_bed'])) {
        // Edit existing bed
        $bed_id = $_POST['bed_id'];
        $bed_number = $_POST['bed_number'];

        $sql = "UPDATE beds SET bed_number = ? WHERE bed_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $bed_number, $bed_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_bed'])) {
        // Delete existing bed
        $bed_id = $_POST['bed_id'];

        $sql = "DELETE FROM beds WHERE bed_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bed_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect to avoid resubmission on page refresh
    header("Location: $_SERVER[PHP_SELF]?page=$page");
    exit;
}

// Fetch beds with pagination
$proctor_block_id = $_SESSION["block_id"];
$sql_beds = "SELECT b.bed_id, b.bed_number, r.room_number, r.room_description 
             FROM beds b 
             INNER JOIN rooms r ON b.room_id = r.room_id 
             WHERE r.block_id = ? 
             LIMIT ?, ?";
$stmt = $conn->prepare($sql_beds);
$stmt->bind_param("iii", $proctor_block_id, $start, $limit);
$stmt->execute();
$result_beds = $stmt->get_result();
$beds = $result_beds->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count total number of beds
$sql_count = "SELECT COUNT(*) AS total 
              FROM beds b 
              INNER JOIN rooms r ON b.room_id = r.room_id 
              WHERE r.block_id = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $proctor_block_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_beds = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_beds / $limit);

// Fetch rooms assigned to the proctor's block
$sql_rooms = "SELECT * FROM rooms WHERE block_id = ?";
$stmt_rooms = $conn->prepare($sql_rooms);
$stmt_rooms->bind_param("i", $proctor_block_id);
$stmt_rooms->execute();
$result_rooms = $stmt_rooms->get_result();
$rooms = $result_rooms->fetch_all(MYSQLI_ASSOC);
$stmt_rooms->close();

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Beds</h2>
        </div>
        <div class="card-body">
            <!-- Pagination -->
            <nav aria-label="Bed navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1) : ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>
                    <?php
                    // Display 10 page numbers
                    $start_page = max(1, $page - 10);
                    $end_page = min($start_page + 19, $total_pages);
                    for ($i = $start_page; $i <= $end_page; $i++) : ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages) : ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addBedModal">
                Add Bed
            </button>

            <!-- Modal for adding bed -->
            <div class="modal fade" id="addBedModal" tabindex="-1" role="dialog" aria-labelledby="addBedModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addBedModalLabel">Add New Bed</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="room_id">Room</label>
                                    <select class="form-control" id="room_id" name="room_id" required>
                                        <option value="">Select Room</option>
                                        <?php foreach ($rooms as $room) : ?>
                                            <option value="<?php echo $room['room_id']; ?>"><?php echo $room['room_number']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="bed_number">Bed Number</label>
                                    <input type="text" class="form-control" id="bed_number" name="bed_number" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_bed">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Display beds -->
            <div class="row">
                <?php foreach ($beds as $bed) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Bed <?php echo $bed['bed_number']; ?></h5>
                                <p class="card-text">Room: <?php echo $bed['room_number']; ?></p>
                                <p class="card-text">Description: <?php echo $bed['room_description']; ?></p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editBedModal<?php echo $bed['bed_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteBedModal<?php echo $bed['bed_id']; ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for editing bed -->
                    <div class="modal fade" id="editBedModal<?php echo $bed['bed_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editBedModalLabel<?php echo $bed['bed_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editBedModalLabel<?php echo $bed['bed_id']; ?>">Edit Bed</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="bed_number">Bed Number</label>
                                            <input type="text" class="form-control" id="bed_number" name="bed_number" value="<?php echo $bed['bed_number']; ?>" required>
                                        </div>
                                        <input type="hidden" name="bed_id" value="<?php echo $bed['bed_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_bed">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for deleting bed -->
                    <div class="modal fade" id="deleteBedModal<?php echo $bed['bed_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteBedModalLabel<?php echo $bed['bed_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteBedModalLabel<?php echo $bed['bed_id']; ?>">Delete Bed</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete bed <?php echo $bed['bed_number']; ?>?
                                        <input type="hidden" name="bed_id" value="<?php echo $bed['bed_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger" name="delete_bed">Delete</button>
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
