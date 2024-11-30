<?php
// Start session and include header
session_start();

// Include database connection
require_once '../includes/db_connection.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    // Redirect unauthorized users to the login page
    header('Location: ../login.php');
    exit;
}

// Fetch data for generating reports
$sql = "SELECT s.student_id, s.username, s.name, s.contact_number, d.department_name 
        FROM student s
        INNER JOIN departments d ON s.department_id = d.department_id";
$result = $conn->query($sql);

// Include header
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h3>Student Reports</h3>
    <div class="row">
        <div class="col">
        <div class="text-center">
                <!-- Print All button -->
                <button class="btn btn-success" onclick="printAll()">Print All</button>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Contact Number</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['student_id']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['contact_number']; ?></td>
                                <td><?php echo $row['department_name']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No student data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
           
        </div>
    </div>
</div>
<?php include '../includes/footer.php';?>

<script>
    function printAll() {
        // Open a new window for printing
        var printWindow = window.open('', '_blank');

        // Construct the content to be printed
        var content = '<html><head><title>Student Report</title>';
        content += '<style>@media print { body { font-family: Arial, sans-serif; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; } th { background-color: #f2f2f2; } } </style>';
        content += '</head><body>';
        content += '<h1 style="text-align: center;">Student Report</h1>';
        content += '<table>';
        content += '<thead><tr><th>Student ID</th><th>Username</th><th>Name</th><th>Contact Number</th><th>Department</th></tr></thead>';
        content += '<tbody>';

        <?php
        // Reset the data seek pointer to the beginning of the result set
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()): ?>
            content += '<tr>';
            content += '<td><?php echo $row['student_id']; ?></td>';
            content += '<td><?php echo $row['username']; ?></td>';
            content += '<td><?php echo $row['name']; ?></td>';
            content += '<td><?php echo $row['contact_number']; ?></td>';
            content += '<td><?php echo $row['department_name']; ?></td>';
            content += '</tr>';
        <?php endwhile; ?>

        content += '</tbody>';
        content += '</table>';
        content += '</body></html>';

        // Write content to the new window
        printWindow.document.open();
        printWindow.document.write(content);
        printWindow.document.close();

        // Print the window
        setTimeout(function () {
            printWindow.print();
            // Close the print window after printing
            printWindow.close();
        }, 500);
    }
</script>
