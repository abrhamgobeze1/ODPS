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

// Fetch proctor's block ID
$proctor_block_id = $_SESSION["block_id"];

// Fetch students assigned to rooms in the proctor's block along with their bed number, college, and department
$sql_students = "SELECT s.student_id, s.username, s.name, s.contact_number, s.gender, s.batch, 
                        b.bed_number, d.department_name, c.college_name, r.room_number, bl.block_name
                 FROM student s
                 INNER JOIN beds bd ON s.student_id = bd.student_id
                 INNER JOIN dormitory_assignments da ON s.student_id = da.student_id
                 INNER JOIN beds b ON da.bed_id = b.bed_id
                 INNER JOIN rooms r ON b.room_id = r.room_id
                 INNER JOIN blocks bl ON r.block_id = bl.block_id
                 INNER JOIN departments d ON s.department_id = d.department_id
                 INNER JOIN colleges c ON d.college_id = c.college_id
                 WHERE bl.block_id = ?";

$stmt_students = $conn->prepare($sql_students);
$stmt_students->bind_param("i", $proctor_block_id);
$stmt_students->execute();
$result_students = $stmt_students->get_result();
$students = $result_students->fetch_all(MYSQLI_ASSOC);
$stmt_students->close();

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">View Student Profiles</h2>
        </div>
        <div class="card-body">
            <?php if (empty($students)) : ?>
                <div class="alert alert-info" role="alert">
                    No students assigned to rooms in this block.
                </div>
            <?php else : ?>
                <div class="d-flex justify-content-end mb-3">
                    <button onclick="printStudentProfiles()" class="btn btn-primary me-2">Print</button>
                    <button onclick="exportToCSV()" class="btn btn-success">Export to CSV</button>
                </div>
                <div id="printableTable">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Contact Number</th>
                                <th>Gender</th>
                                <th>Batch</th>
                                <th>Block</th>
                                <th>Room Number</th>
                                <th>Bed Number</th>
                                <th>College</th>
                                <th>Department</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student) : ?>
                                <tr>
                                    <td><?php echo $student['student_id']; ?></td>
                                    <td><?php echo $student['username']; ?></td>
                                    <td><?php echo $student['name']; ?></td>
                                    <td><?php echo $student['contact_number']; ?></td>
                                    <td><?php echo $student['gender']; ?></td>
                                    <td><?php echo $student['batch']; ?></td>
                                    <td><?php echo $student['block_name']; ?></td>
                                    <td><?php echo $student['room_number']; ?></td>
                                    <td><?php echo $student['bed_number']; ?></td>
                                    <td><?php echo $student['college_name']; ?></td>
                                    <td><?php echo $student['department_name']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    function printStudentProfiles() {
        var printContents = document.getElementById('printableTable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    function exportToCSV() {
        var data = [['Student ID', 'Username', 'Name', 'Contact Number', 'Gender', 'Batch', 'Block', 'Room Number', 'Bed Number', 'College', 'Department']];

        <?php foreach ($students as $student) : ?>
            data.push([
                '<?php echo $student['student_id']; ?>',
                '<?php echo $student['username']; ?>',
                '<?php echo $student['name']; ?>',
                '<?php echo $student['contact_number']; ?>',
                '<?php echo $student['gender']; ?>',
                '<?php echo $student['batch']; ?>',
                '<?php echo $student['block_name']; ?>',
                '<?php echo $student['room_number']; ?>',
                '<?php echo $student['bed_number']; ?>',
                '<?php echo $student['college_name']; ?>',
                '<?php echo $student['department_name']; ?>'
            ]);
        <?php endforeach; ?>

        var csvContent = "data:text/csv;charset=utf-8,";
        data.forEach(function(rowArray) {
            var row = rowArray.join(",");
            csvContent += row + "\n";
        });

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "student_profiles.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<?php
// Include footer
include_once '../includes/footer.php';
?>