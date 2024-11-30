<?php
// Include database connection file
include_once '../includes/db_connection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as a student, if not redirect to the login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "student") {
    header("Location: ../login.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $notice_id = $_POST["notice_id"];
    $student_id = $_SESSION["student_id"]; // Assuming you store student_id in the session

    $comment_content = $_POST["comment_content"];

    // Insert the comment into the database
    $sql = "INSERT INTO comments (notice_id, student_id, comment_content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $notice_id, $student_id, $comment_content);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the page after adding the comment
    header("Location: view_notices.php");
    exit;
} else {
    // If the request method is not POST, redirect to an error page or handle accordingly
    header("Location: ../error.php");
    exit;
}
?>
