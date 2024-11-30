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

// Fetch comments for notices posted by students assigned to the manager's dormitory
$sql_comments = "SELECT comments.comment_id, comments.comment_content, comments.comment_posted_on, 
                 student.name AS student_name 
                 FROM comments 
                 INNER JOIN notices ON comments.notice_id = notices.notice_id 
                 INNER JOIN student ON comments.student_id = student.student_id
                 INNER JOIN dormitory_assignments ON student.student_id = dormitory_assignments.student_id
                 INNER JOIN beds ON dormitory_assignments.bed_id = beds.bed_id
                 INNER JOIN rooms ON beds.room_id = rooms.room_id
                 INNER JOIN blocks ON rooms.block_id = blocks.block_id
                 WHERE blocks.dormitory_id = ?";

$stmt_comments = $conn->prepare($sql_comments);
if (!$stmt_comments) {
    echo "Error preparing statement: " . $conn->error;
    exit;
}
$stmt_comments->bind_param("i", $_SESSION["dormitory_id"]);
$stmt_comments->execute();
$result_comments = $stmt_comments->get_result();
if (!$result_comments) {
    echo "Error fetching comments: " . $conn->error;
    exit;
}
$comments = $result_comments->fetch_all(MYSQLI_ASSOC);
$stmt_comments->close();

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Comments</h2>
        </div>
        <div class="card-body">
            <!-- Display comments -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Notice Title</th>
                            <th>Comment</th>
                            <th>Student Name</th>
                            <th>Posted On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td><?php echo $comment['notice_title']; ?></td>
                                <td><?php echo $comment['comment_content']; ?></td>
                                <td><?php echo $comment['student_name']; ?></td>
                                <td><?php echo $comment['comment_posted_on']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>
