<?php
// Include header
// Start the session
session_start();

// Check if the user is logged in as admin, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "proctor") {
    header("Location: ../login.php");
    exit;
}
require_once '../includes/db_connection.php';

// Fetch notices from the database
$sql = "SELECT * FROM notices ORDER BY notice_posted_on DESC";
$result = $conn->query($sql);

// Initialize notices array
$notices = array();

if ($result->num_rows > 0) {
    // Fetch each row and store in notices array
    while ($row = $result->fetch_assoc()) {
        $notices[] = $row;
    }
}

// Check if a new notice should be added
// Check if a new notice should be added
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_notice'])) {
    $notice_title = $_POST['title'];
    $notice_content = $_POST['notice_content'];
    $notice_images = $_FILES['notice_images']['name'];
    $tmp_name = $_FILES['notice_images']['tmp_name'];
    $image_dir = '../images/notices/';

    // Check if notice_images was uploaded without errors
    if ($_FILES['notice_images']['error'] == UPLOAD_ERR_OK) {
        $image_path = $image_dir . basename($notice_images);
        if (move_uploaded_file($tmp_name, $image_path)) {
            // Get logged-in user's information
            $posted_by = $_SESSION['username']; // Assuming username is stored in the session
            $posted_role = $_SESSION['user_type']; // Assuming role is stored in the session

            $sql = "INSERT INTO notices (notice_title, notice_content, notice_images, notice_posted_by, notice_posted_role) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $notice_title, $notice_content, $notice_images, $posted_by, $posted_role);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Notice added successfully.";
                header("Location: manage_notices.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Error adding notice: " . $conn->error;
            }
        } else {
            $_SESSION['error_message'] = "Error uploading notice_images.";
        }
    } else {
        $_SESSION['error_message'] = "Error uploading notice_images.";
    }
}


require_once '../includes/header.php';
?>

<!-- Display notices -->
<div class="container mt-5">
    <h2 class="text-center mb-4">Notices</h2>

    <!-- Display success/error messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['success_message']; ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Add new notice button -->
    <div class="text-center mb-4">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNoticeModal">
            Add New Notice
        </button>
    </div>

    <div class="row">
        <?php if (!empty($notices)): ?>
            <?php foreach ($notices as $notice): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($notice['notice_images'])): ?>
                            <img src="../images/notices/<?php echo $notice['notice_images']; ?>" class="card-img-top"
                                alt="<?php echo $notice['notice_title']; ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $notice['notice_title']; ?></h5>
                            <div class="card-text" id="notice-notice_content-<?php echo $notice['notice_id']; ?>">
                                <?php echo substr($notice['notice_content'], 0, 100) . '...'; ?>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary stretched-link read-more-btn" data-toggle="modal"
                                data-target="#readMoreModal-<?php echo $notice['notice_id']; ?>"
                                data-notice-id="<?php echo $notice['notice_id']; ?>">
                                Read More
                            </button>
                            <div>
                                <small class="text-muted">Posted by <?php echo $notice['notice_posted_by']; ?></small>
                            </div>
                            <div>
                                <small class="text-muted">Posted Role <?php echo $notice['notice_posted_role']; ?></small>
                            </div>
                            <div>
                                <small class="text-muted">Posted on <?php echo $notice['notice_posted_on']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Read More Modal -->
                <div class="modal fade" id="readMoreModal-<?php echo $notice['notice_id']; ?>" tabindex="-1" role="dialog"
                    aria-labelledby="readMoreModalLabel-<?php echo $notice['notice_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="readMoreModalLabel-<?php echo $notice['notice_id']; ?>">
                                    <?php echo $notice['notice_title']; ?>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?php if (!empty($notice['notice_images'])): ?>
                                    <img src="../images/notices/<?php echo $notice['notice_images']; ?>" class="card-img-top"
                                        alt="<?php echo $notice['notice_title']; ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $notice['notice_title']; ?></h5>
                                    <div class="card-text" id="notice-notice_content-<?php echo $notice['notice_id']; ?>">
                                        <?php
                                        require_once '../includes/Parsedown.php';
                                        $parsedown = new Parsedown();
                                        echo $parsedown->text($notice['notice_content']);
                                        ?>
                                    </div>
                                    <div>
                                        <small class="text-muted">Posted by <?php echo $notice['notice_posted_by']; ?></small>
                                    </div>
                                    <div>
                                        <small class="text-muted">Posted Role
                                            <?php echo $notice['notice_posted_role']; ?></small>
                                    </div>
                                    <div>
                                        <small class="text-muted">Posted on <?php echo $notice['notice_posted_on']; ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No notices found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Add Notice Modal -->
<div class="modal fade" id="addNoticeModal" tabindex="-1" role="dialog" aria-labelledby="addNoticeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> <!-- Added missing div with class modal-content -->
            <div class="modal-header">
                <h5 class="modal-title" id="addNoticeModalLabel">Add New Notice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="manage_notices.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="notice_content">Content</label>
                        <textarea class="form-control" id="notice_content" name="notice_content" rows="5"
                            required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="notice_images">Image</label>
                        <input type="file" class="form-control-file" id="notice_images" name="notice_images">
                    </div>
            </div> <!-- Closed modal-body div here -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" name="add_notice">Add Notice</button>
            </div>
            </form> <!-- Moved closing form tag here -->
        </div> <!-- Closed modal-content div here -->
    </div>
</div>


<?php require_once '../includes/footer.php'; ?>

<script src="../js/marked.min.js"></script>
<script src="../js/purify.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        <?php foreach ($notices as $notice): ?>
            var markdownContent = `<?php echo addslashes($notice['notice_content']); ?>`;
            var htmlContent = marked(markdownContent);
            var sanitizedContent = DOMPurify.sanitize(htmlContent);
            document.getElementById("notice-notice_content-<?php echo $notice['notice_id']; ?>").innerHTML = sanitizedContent;
        <?php endforeach; ?>
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Existing code for displaying the notice_content
        <?php foreach ($notices as $notice): ?>
            var markdownContent = `<?php echo addslashes($notice['notice_content']); ?>`;
            var htmlContent = marked(markdownContent);
            var sanitizedContent = DOMPurify.sanitize(htmlContent);
            document.getElementById("notice-notice_content-<?php echo $notice['notice_id']; ?>").innerHTML = sanitizedContent.substring(0, 100) + "...";
        <?php endforeach; ?>

        // Add event listener for "Read More" button clicks
        var readMoreButtons = document.querySelectorAll(".read-more-btn");
        readMoreButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                var noticeId = this.dataset.noticeId;
                var fullNoticeContent = document.getElementById("notice-notice_content-" + noticeId).innerHTML;
                var fullNoticeTitle = document.querySelector("#notice-notice_content-" + noticeId + " + .card-footer .card-title").textContent;

                // Update the modal notice_content
                document.getElementById("fullNoticeModalLabel").textContent = fullNoticeTitle;
                document.getElementById("full-notice-notice_content").innerHTML = fullNoticeContent;

                // Show the modal
                $("#fullNoticeModal").modal("show");
            });
        });
    });
</script>