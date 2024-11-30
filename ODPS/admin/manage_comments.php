<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as an admin, if not redirect to the login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// Handle comment deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_comment_id"])) {
    $delete_comment_id = sanitize_input($_POST["delete_comment_id"]);

    $delete_sql = "DELETE FROM comments WHERE comment_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_comment_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    header("Location: manage_comments.php");
    exit;
}


// Function to sanitize user inputs
function sanitize_input($input)
{
    return htmlspecialchars(strip_tags($input));
}

// Function to display comments
function display_comments($conn)
{
    $output = '';
    $sql = "SELECT comments.*, notices.notice_title, student.name AS student_name 
            FROM comments 
            JOIN notices ON comments.notice_id = notices.notice_id
            JOIN student ON comments.student_id = student.student_id
            ORDER BY comments.comment_posted_on DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $comment_id = $row["comment_id"];
            $comment_content = $row["comment_content"];
            $comment_posted_on = $row["comment_posted_on"];
            $notice_title = $row["notice_title"];
            $student_name = $row["student_name"];

            $output .= '<tr>';
            $output .= '<td>' . substr($comment_content, 0, 50) . ' <a href="#" class="read-more" data-toggle="modal" data-target="#commentModal-' . $comment_id . '">Read More</a></td>';
            $output .= '<td>' . $notice_title . '</td>';
            $output .= '<td>' . $student_name . '</td>';
            $output .= '<td>' . date("F j, Y, g:i a", strtotime($comment_posted_on)) . '</td>';
            $output .= '<td>';
            $output .= '<button type="button" class="btn btn-danger btn-sm delete-comment-btn" data-toggle="modal" data-target="#deleteCommentModal" data-comment-id="' . $comment_id . '">Delete</button>';
            $output .= '</td>';
            $output .= '</tr>';

            // Modal for displaying full comment content
            $output .= '<div class="modal fade" id="commentModal-' . $comment_id . '" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel-' . $comment_id . '" aria-hidden="true">';
            $output .= '<div class="modal-dialog" role="document">';
            $output .= '<div class="modal-content">';
            $output .= '<div class="modal-header">';
            $output .= '<h5 class="modal-title" id="commentModalLabel-' . $comment_id . '">Comment Content</h5>';
            $output .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
            $output .= '<span aria-hidden="true">&times;</span>';
            $output .= '</button>';
            $output .= '</div>';
            $output .= '<div class="modal-body">';
            $output .= '<p>' . $comment_content . '</p>';
            $output .= '</div>';
            $output .= '<div class="modal-footer">';
            $output .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
        }
    } else {
        $output .= '<tr><td colspan="5">No comments found.</td></tr>';
    }
    return $output;
}

// Function to display notices
function display_notices($conn)
{
    $output = '';
    $sql = "SELECT * FROM notices ORDER BY notice_posted_on DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notice_id = $row["notice_id"];
            $notice_title = $row["notice_title"];
            $notice_content = $row["notice_content"];
            $notice_images = $row["notice_images"];
            $notice_posted_by = $row["notice_posted_by"];
            $notice_posted_role = $row["notice_posted_role"];
            $notice_posted_on = $row["notice_posted_on"];

            $output .= '<tr>';
            $output .= '<td>' . $notice_title . '</td>';
            $output .= '<td>' . substr($notice_content, 0, 50) . ' <a href="#" class="read-more" data-toggle="modal" data-target="#noticeModal-' . $notice_id . '">Read More</a></td>';
            $output .= '<td>' . $notice_posted_by . '</td>';
            $output .= '<td>' . $notice_posted_role . '</td>';
            $output .= '<td>' . date("F j, Y, g:i a", strtotime($notice_posted_on)) . '</td>';
            $output .= '<td>';
            $output .= '<button type="button" class="btn btn-danger btn-sm delete-notice-btn" data-toggle="modal" data-target="#deleteNoticeModal" data-notice-id="' . $notice_id . '">Delete</button>';
            $output .= '</td>';
            $output .= '</tr>';

            // Modal for displaying full notice content
            $output .= '<div class="modal fade" id="noticeModal-' . $notice_id . '" tabindex="-1" role="dialog" aria-labelledby="noticeModalLabel-' . $notice_id . '" aria-hidden="true">';
            $output .= '<div class="modal-dialog" role="document">';
            $output .= '<div class="modal-content">';
            $output .= '<div class="modal-header">';
            $output .= '<h5 class="modal-title" id="noticeModalLabel-' . $notice_id . '">Notice Content</h5>';
            $output .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
            $output .= '<span aria-hidden="true">&times;</span>';
            $output .= '</button>';
            $output .= '</div>';
            $output .= '<div class="modal-body">';
            $output .= '<p>' . $notice_content . '</p>';
            if (!empty($notice_images)) {
                $output .= '<img src="../images/notices/' . $notice_images . '" class="img-fluid">';
            }
            $output .= '</div>';
            $output .= '<div class="modal-footer">';
            $output .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
        }
    } else {
        $output .= '<tr><td colspan="6">No notices found.</td></tr>';
    }
    return $output;
}
// Include the header
include_once '../includes/header.php';
?>
<div class="container my-4">
    <h1 class="mb-4">Manage Comments</h1>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Comment Content</th>
                    <th>Notice Title</th>
                    <th>Student Name</th>
                    <th>Posted On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php echo display_comments($conn); ?>
            </tbody>
        </table>
    </div>

    <h1 class="mb-4 mt-5">Manage Notices</h1>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Notice Title</th>
                    <th>Notice Content</th>
                    <th>Posted By</th>
                    <th>Posted Role</th>
                    <th>Posted On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php echo display_notices($conn); ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Comment Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1" role="dialog" aria-labelledby="deleteCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCommentModalLabel">Delete Comment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this comment?</p>
            </div>
            <div class="modal-footer">
                <form id="delete-comment-form" method="post">
                    <input type="hidden" id="delete-comment-id" name="delete_comment_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Notice Modal -->
<div class="modal fade" id="deleteNoticeModal" tabindex="-1" role="dialog" aria-labelledby="deleteNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteNoticeModalLabel">Delete Notice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this notice?</p>
            </div>
            <div class="modal-footer">
                <form id="delete-notice-form" method="post">
                    <input type="hidden" id="delete-notice-id" name="delete_notice_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?php
// Include the footer
include_once '../includes/footer.php';
?>

<script>
    // Handle delete comment modal
    $('.delete-comment-btn').click(function() {
        var commentId = $(this).data('comment-id');
        $('#delete-comment-id').val(commentId);
    });

    // Handle delete notice modal
    $('.delete-notice-btn').click(function() {
        var noticeId = $(this).data('notice-id');
        $('#delete-notice-id').val(noticeId);
    });
</script>