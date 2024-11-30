<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as admin, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "system_admin") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Fetch all assignment details
$sql_assignments = "SELECT 
                        dormitory_assignments.assignment_id, 
                        student.name, 
                        student.username, 
                        student.gender, 
                        departments.department_name, 
                        colleges.college_name, 
                        colleges.college_type, 
                        dormitories.dormitory_name, 
                        blocks.block_name, 
                        rooms.room_number, 
                        beds.bed_number, 
                        dormitory_assignments.assignment_start_date, 
                        dormitory_assignments.assignment_end_date
                    FROM dormitory_assignments
                    INNER JOIN student ON dormitory_assignments.student_id = student.student_id
                    INNER JOIN departments ON student.department_id = departments.department_id
                    INNER JOIN colleges ON departments.college_id = colleges.college_id
                    INNER JOIN beds ON dormitory_assignments.bed_id = beds.bed_id
                    INNER JOIN rooms ON beds.room_id = rooms.room_id
                    INNER JOIN blocks ON rooms.block_id = blocks.block_id
                    INNER JOIN dormitories ON blocks.dormitory_id = dormitories.dormitory_id";
$stmt_assignments = $conn->prepare($sql_assignments);
$stmt_assignments->execute();
$result_assignments = $stmt_assignments->get_result();
$assignments = $result_assignments->fetch_all(MYSQLI_ASSOC);
$stmt_assignments->close();

// Populate the departments and dormitories arrays
$departments = array_unique(array_column($assignments, 'department_name'));
$dormitories = array_unique(array_column($assignments, 'dormitory_name'));

// Include header
include_once '../includes/header.php';
?>

<main class="mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Print Assignments</h2>
        </div>

        <div class="card-body">
            <!-- Print Buttons -->
            <button class="btn btn-primary mb-3" onclick="printTable()">Print PDF Table</button>
            <button class="btn btn-primary mb-3" onclick="printCSVTable()">Print CSV Table</button>

            <!-- Assignment Table -->
            <?php if (empty($assignments)): ?>
                <p>No assignments found.</p>
            <?php else: ?>
                <table id="assignmentsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Dormitory</th>
                            <th>Assignment ID</th>
                            <th>Student Name</th>
                            <th>Username</th>
                            <th>Gender</th>
                            <th>College</th>
                            <th>College Type</th>
                            <th>Block</th>
                            <th>Room Number</th>
                            <th>Bed Number</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $department): ?>
                            <tr>
                                <td colspan="13" class="bg-light font-weight-bold"><?php echo $department; ?></td>
                            </tr>
                            <?php foreach ($dormitories as $dormitory): ?>
                                <tr>
                                    <td></td>
                                    <td colspan="12" class="bg-light font-weight-bold"><?php echo $dormitory; ?></td>
                                </tr>
                                <?php foreach ($assignments as $assignment): ?>
                                    <?php if ($assignment['department_name'] == $department && $assignment['dormitory_name'] == $dormitory): ?>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td><?php echo $assignment['assignment_id']; ?></td>
                                            <td><?php echo $assignment['name']; ?></td>
                                            <td><?php echo $assignment['username']; ?></td>
                                            <td><?php echo $assignment['gender']; ?></td>
                                            <td><?php echo $assignment['college_name']; ?></td>
                                            <td><?php echo $assignment['college_type']; ?></td>
                                            <td><?php echo $assignment['block_name']; ?></td>
                                            <td><?php echo $assignment['room_number']; ?></td>
                                            <td><?php echo $assignment['bed_number']; ?></td>
                                            <td><?php echo $assignment['assignment_start_date']; ?></td>
                                            <td><?php echo $assignment['assignment_end_date']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>

<script>
    function printTable() {
        var printContents = document.getElementById("assignmentsTable").outerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>



<script>
    function printCSVTable() {
        var data = [
            ['Assignment ID', 'Student Name', 'Username', 'Gender', 'Department', 'College', 'College Type', 'Dormitory', 'Block', 'Room Number', 'Bed Number', 'Start Date', 'End Date']
        ];

        <?php foreach ($assignments as $assignment): ?>
            data.push([
                '<?php echo $assignment['assignment_id']; ?>',
                '<?php echo $assignment['name']; ?>',
                '<?php echo $assignment['username']; ?>',
                '<?php echo $assignment['gender']; ?>',
                '<?php echo $assignment['department_name']; ?>',
                '<?php echo $assignment['college_name']; ?>',
                '<?php echo $assignment['college_type']; ?>',
                '<?php echo $assignment['dormitory_name']; ?>',
                '<?php echo $assignment['block_name']; ?>',
                '<?php echo $assignment['room_number']; ?>',
                '<?php echo $assignment['bed_number']; ?>',
                '<?php echo $assignment['assignment_start_date']; ?>',
                '<?php echo $assignment['assignment_end_date']; ?>'
            ]);
        <?php endforeach; ?>

        var csvContent = "data:text/csv;charset=utf-8,";
        data.forEach(function(rowArray) {
            var row = rowArray.join(",");
            csvContent += row + "\r\n";
        });

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "assignments.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>