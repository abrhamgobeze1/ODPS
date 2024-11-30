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

// Check if form is submitted for adding or editing a collage
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new college
    if (isset($_POST['add_collage'])) {
        $college_name = $_POST['college_name'];
        $college_type = $_POST['college_type'];
        $sql = "INSERT INTO colleges (college_name, college_type) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $college_name, $college_type);
        $stmt->execute();
        $stmt->close();
    }

    // Edit existing college
    if (isset($_POST['edit_collage'])) {
        $college_name = $_POST['college_name'];
        $college_type = $_POST['college_type'];
        $college_id = $_POST['college_id'];
        $sql = "UPDATE colleges SET college_name = ?, college_type = ? WHERE college_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $college_name, $college_type, $college_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_collage'])) {
        // Delete existing collage
        $college_id = $_POST['college_id'];
        $sql = "DELETE FROM colleges WHERE college_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $college_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch colleges
$sql = "SELECT college_id, college_name,college_type FROM colleges";
$result = $conn->query($sql);
$colleges = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $colleges[] = $row;
    }
}
// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Colleges</h2>
        </div>
        <div class="card-body">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addCollageModal">
                Add College
            </button>

            <!-- Modal for adding College -->
            <div class="modal fade" id="addCollageModal" tabindex="-1" role="dialog"
                aria-labelledby="addCollageModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCollageModalLabel">Add New College</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="college_name">College Name</label>
                                    <input type="text" class="form-control" id="college_name" name="college_name"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="college_type">College Type</label>
                                    <select class="form-control" id="college_type" name="college_type" required>
                                        <option value="Natural">Natural</option>
                                        <option value="Social">Social</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_collage">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Display colleges -->
            <div class="row">
                <?php foreach ($colleges as $college): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                            <h5 class="card-title"><?php echo $college['college_name']; ?></h5>
                            <h3 class="card-title"><?php echo $college['college_type']; ?></h5>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#editCollageModal<?php echo $college['college_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#deleteCollageModal<?php echo $college['college_id']; ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for editing college -->
                    <div class="modal fade" id="editCollageModal<?php echo $college['college_id']; ?>" tabindex="-1"
                        role="dialog" aria-labelledby="editCollageModalLabel<?php echo $college['college_id']; ?>"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="editCollageModalLabel<?php echo $college['college_id']; ?>">Edit College
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="college_name">College Name</label>
                                            <input type="text" class="form-control" id="college_name" name="college_name"
                                                value="<?php echo $college['college_name']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="college_type">College Type</label>
                                            <select class="form-control" id="college_type" name="college_type" required>
                                                <option value="Natural">Natural</option>
                                                <option value="Social">Social</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="college_id"
                                            value="<?php echo $college['college_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_collage">Save
                                            changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for deleting college -->
                    <div class="modal fade" id="deleteCollageModal<?php echo $college['college_id']; ?>" tabindex="-1"
                        role="dialog" aria-labelledby="deleteCollageModalLabel<?php echo $college['college_id']; ?>"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="deleteCollageModalLabel<?php echo $college['college_id']; ?>">Delete College
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete the college
                                        "<?php echo $college['college_name']; ?>"?
                                        <input type="hidden" name="college_id"
                                            value="<?php echo $college['college_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger" name="delete_collage">Delete</button>
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