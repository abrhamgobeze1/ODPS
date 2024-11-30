<?php

ob_start();

// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as a student, if not redirect to the login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "student") {
    header("Location: ../login.php");
    exit;
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $notice_id = sanitize_input($_POST["notice_id"]);
    $student_id = $_SESSION["user_id"]; // Assuming you store student_id in the session
    $comment_content = sanitize_input($_POST["comment_content"]);

    // Insert the comment into the database
    $sql = "INSERT INTO comments (notice_id, student_id, comment_content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $notice_id, $student_id, $comment_content);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the page after adding the comment
    header("Location: view_notices.php");
    exit;
}

// Include the header
include_once '../includes/header.php';

// Function to sanitize user inputs
function sanitize_input($input)
{
    return htmlspecialchars(strip_tags($input));
}

// Function to display comments
function display_comments($conn, $notice_id)
{
    $output = '';
    $commentSql = "SELECT comments.*, student.name AS student_name 
                   FROM comments
                   JOIN student ON comments.student_id = student.student_id
                   WHERE comments.notice_id = ?
                   ORDER BY comments.comment_posted_on DESC";
    $commentStmt = $conn->prepare($commentSql);
    $commentStmt->bind_param("i", $notice_id);
    $commentStmt->execute();
    $commentResult = $commentStmt->get_result();

    if ($commentResult->num_rows > 0) {
        while ($commentRow = $commentResult->fetch_assoc()) {
            $comment_content = $commentRow["comment_content"];
            $comment_posted_by = $commentRow["student_name"];
            $comment_posted_on = $commentRow["comment_posted_on"];
            $output .= '<div class="media mb-3">';
            $output .= '<div class="media-body">';
            $output .= '<h5 class="mt-0">' . $comment_posted_by . '</h5>';
            $output .= '<p>' . $comment_content . '</p>';
            $output .= '<small>Posted on ' . date("F j, Y, g:i a", strtotime($comment_posted_on)) . '</small>';
            $output .= '</div></div>';
        }
    } else {
        $output .= "<p>No comments found.</p>";
    }
    $commentStmt->close();
    return $output;
}
ob_end_flush();

?>

<div class="container my-4">
    <h1 class="mb-4">View Notices</h1>

    <div class="row">
        <?php
        // Fetch all the notices from the database
        $sql = "SELECT * FROM notices ORDER BY notice_posted_on DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $notice_id = $row["notice_id"];
                $notice_title = $row["notice_title"];
                $notice_content = substr($row["notice_content"], 0, 30) . '...'; // Display only the first 30 characters
                $notice_images = $row["notice_images"];
                $notice_posted_by = $row["notice_posted_by"];
                $notice_posted_role = $row["notice_posted_role"];
                $notice_posted_on = $row["notice_posted_on"];
        ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4><?php echo $notice_title; ?></h4>
                            <small>Posted by <?php echo $notice_posted_by; ?> (<?php echo $notice_posted_role; ?>) on
                                <?php echo date("F j, Y, g:i a", strtotime($notice_posted_on)); ?></small>
                        </div>
                        <div class="card-body">
                            <?php if ($notice_images) { ?>
                                <div class="notice-images">
                                    <?php $images = explode(",", $notice_images);
                                    foreach ($images as $image) { ?>
                                        <img style="width: 400px; height: 300px; object-fit: cover;" src="<?php echo "../images/notices/" . trim($image); ?>" class="img-fluid mb-2" alt="Notice Image">
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <div id="card-notice-content-<?php echo $notice_id; ?>"></div>

                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#noticeModal-<?php echo $notice_id; ?>">
                                Read More
                            </button>
                            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#commentModal-<?php echo $notice_id; ?>">
                                Read Comments
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="noticeModal-<?php echo $notice_id; ?>" tabindex="-1" role="dialog" aria-labelledby="noticeModalLabel-<?php echo $notice_id; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="noticeModalLabel-<?php echo $notice_id; ?>"><?php echo $notice_title; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?php if ($notice_images) { ?>
                                    <div class="notice-images">
                                        <?php $images = explode(",", $notice_images);
                                        foreach ($images as $image) { ?>
                                            <img src="<?php echo "../images/notices/" . trim($image); ?>" class="img-fluid mb-2" style="width: 400px; height: 300px; object-fit: cover;" alt="Notice Image">
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <div id="notice-content-<?php echo $notice_id; ?>"></div>

                                <small>Posted by <?php echo $notice_posted_by; ?> (<?php echo $notice_posted_role; ?>) on
                                    <?php echo date("F j, Y, g:i a", strtotime($notice_posted_on)); ?></small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Comment Modal -->
                <div class="modal fade" id="commentModal-<?php echo $notice_id; ?>" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel-<?php echo $notice_id; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="commentModalLabel-<?php echo $notice_id; ?>">Comments for Notice: <?php echo $notice_title; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="comment-list-<?php echo $notice_id; ?>">
                                    <?php echo display_comments($conn, $notice_id); ?>
                                </div>
                                <!-- Comment Form -->
                                <form id="comment-form-<?php echo $notice_id; ?>" class="mt-3" method="post" action="">
                                    <div class="form-group">
                                        <label for="comment-content-<?php echo $notice_id; ?>">Add Comment:</label>
                                        <textarea class="form-control" id="comment-content-<?php echo $notice_id; ?>" name="comment_content" rows="3" required></textarea>
                                    </div>
                                    <input type="hidden" name="notice_id" value="<?php echo $notice_id; ?>">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var noticeContent = `<?php echo $notice_content; ?>`;
                        var sanitizedContent = DOMPurify.sanitize(marked.parse(noticeContent));
                        document.getElementById('card-notice-content-<?php echo $notice_id; ?>').innerHTML = sanitizedContent;
                        document.getElementById('notice-content-<?php echo $notice_id; ?>').innerHTML = DOMPurify.sanitize(marked.parse(<?php echo json_encode($row["notice_content"]); ?>));
                    });
                </script>
        <?php
            }
        } else {
            echo "<p>No notices found.</p>";
        }
        ?>
    </div>
</div>

<?php


// Include the footer
include_once '../includes/footer.php';
?>

<!-- Include the marked.min.js and purify.min.js libraries -->
<script src="../js/marked.min.js"></script>
<script src="../js/purify.min.js"></script>