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

// Pagination variables
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$items_per_page = 30;
$offset = ($page - 1) * $items_per_page;

// Sorting and search parameters
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'dormitory_name';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';
$search_dormitory = isset($_GET['search_dormitory']) ? $_GET['search_dormitory'] : '';
$search_gender = isset($_GET['search_gender']) ? $_GET['search_gender'] : '';
$search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$search_department = isset($_GET['search_department']) ? $_GET['search_department'] : '';
$search_college = isset($_GET['search_college']) ? $_GET['search_college'] : '';

// Fetch assignment details for the current page
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
                    INNER JOIN dormitories ON blocks.dormitory_id = dormitories.dormitory_id
                    WHERE dormitories.dormitory_name LIKE CONCAT('%', ?, '%')
                    AND student.gender LIKE CONCAT('%', ?, '%')
                    AND student.name LIKE CONCAT('%', ?, '%')
                    AND departments.department_name LIKE CONCAT('%', ?, '%')
                    AND colleges.college_name LIKE CONCAT('%', ?, '%')
                    ORDER BY $sort_by $sort_order
                    LIMIT ?, ?";
$stmt_assignments = $conn->prepare($sql_assignments);
$stmt_assignments->bind_param("sssssss", $search_dormitory, $search_gender, $search_name, $search_department, $search_college, $offset, $items_per_page);
$stmt_assignments->execute();
$result_assignments = $stmt_assignments->get_result();
$assignments = $result_assignments->fetch_all(MYSQLI_ASSOC);
$stmt_assignments->close();

// Count total number of assignments
$sql_count_assignments = "SELECT COUNT(*) AS total_assignments FROM dormitory_assignments 
                          INNER JOIN student ON dormitory_assignments.student_id = student.student_id
                          INNER JOIN departments ON student.department_id = departments.department_id
                          INNER JOIN colleges ON departments.college_id = colleges.college_id
                          INNER JOIN beds ON dormitory_assignments.bed_id = beds.bed_id
                          INNER JOIN rooms ON beds.room_id = rooms.room_id
                          INNER JOIN blocks ON rooms.block_id = blocks.block_id
                          INNER JOIN dormitories ON blocks.dormitory_id = dormitories.dormitory_id
                          WHERE dormitories.dormitory_name LIKE CONCAT('%', ?, '%')
                          AND student.gender LIKE CONCAT('%', ?, '%')
                          AND student.name LIKE CONCAT('%', ?, '%')
                          AND departments.department_name LIKE CONCAT('%', ?, '%')
                          AND colleges.college_name LIKE CONCAT('%', ?, '%')";
$stmt_count_assignments = $conn->prepare($sql_count_assignments);
$stmt_count_assignments->bind_param("sssss", $search_dormitory, $search_gender, $search_name, $search_department, $search_college);
$stmt_count_assignments->execute();
$result_count_assignments = $stmt_count_assignments->get_result();
$total_assignments = $result_count_assignments->fetch_assoc()['total_assignments'];
$stmt_count_assignments->close();
$total_pages = ceil($total_assignments / $items_per_page);

// Pagination logic
$max_visible_pages = 29;
$half_max_visible_pages = floor($max_visible_pages / 2);
$start_page = max(1, $page - $half_max_visible_pages);
$end_page = min($total_pages, $start_page + $max_visible_pages - 1);

// Include header
include_once '../includes/header.php';
?>

<main class="mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">View Assignments</h2>
        </div>

        <!-- Search and Sorting -->
        <div class="card-header">
            <form class="form-inline" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group mr-3">
                    <label for="search_dormitory">Search by Dormitory:</label>
                    <input type="text" class="form-control" id="search_dormitory" name="search_dormitory"
                        value="<?php echo $search_dormitory; ?>">
                </div>
                <div class="form-group mr-3">
                    <label for="search_gender">Search by Gender:</label>
                    <select class="form-control" id="search_gender" name="search_gender">
                        <option value="">All</option>
                        <option value="Male" <?php echo ($search_gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($search_gender == 'Female') ? 'selected' : ''; ?>>Female
                        </option>
                    </select>
                </div>
                <div class="form-group mr-3">
                    <label for="search_name">Search by Name:</label>
                    <input type="text" class="form-control" id="search_name" name="search_name"
                        value="<?php echo $search_name; ?>">
                </div>
                <div class="form-group mr-3">
                    <label for="search_department">Search by Department:</label>
                    <input type="text" class="form-control" id="search_department" name="search_department"
                        value="<?php echo $search_department; ?>">
                </div>
                <div class="form-group mr-3">
                    <label for="search_college">Search by College:</label>
                    <input type="text" class="form-control" id="search_college" name="search_college"
                        value="<?php echo $search_college; ?>">
                </div>
                <div class="form-group mr-3">
                    <label for="sort_by">Sort by:</label>
                    <select class="form-control" id="sort_by" name="sort_by">
                        <option value="dormitory_name" <?php echo ($sort_by == 'dormitory_name') ? 'selected' : ''; ?>>
                            Dormitory</option>
                        <option value="gender" <?php echo ($sort_by == 'gender') ? 'selected' : ''; ?>>Gender</option>
                        <option value="name" <?php echo ($sort_by == 'name') ? 'selected' : ''; ?>>Name</option>
                        <option value="department_name" <?php echo ($sort_by == 'department_name') ? 'selected' : ''; ?>>
                            Department</option>
                        <option value="college_name" <?php echo ($sort_by == 'college_name') ? 'selected' : ''; ?>>College
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sort_order">Order:</label>
                    <select class="form-control" id="sort_order" name="sort_order">
                        <option value="ASC" <?php echo ($sort_order == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                        <option value="DESC" <?php echo ($sort_order == 'DESC') ? 'selected' : ''; ?>>Descending</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary ml-3">Search and Sort</button>
            </form>
        </div>



        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?page=<?php echo $page - 1; ?>&sort_by=<?php echo $sort_by; ?>&sort_order=<?php echo $sort_order; ?>&search_dormitory=<?php echo $search_dormitory; ?>&search_gender=<?php echo $search_gender; ?>&search_name=<?php echo $search_name; ?>&search_department=<?php echo $search_department; ?>&search_college=<?php echo $search_college; ?>"
                                aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $i; ?>&sort_by=<?php echo $sort_by; ?>&sort_order=<?php echo $sort_order; ?>&search_dormitory=<?php echo $search_dormitory; ?>&search_gender=<?php echo $search_gender; ?>&search_name=<?php echo $search_name; ?>&search_department=<?php echo $search_department; ?>&search_college=<?php echo $search_college; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?page=<?php echo $page + 1; ?>&sort_by=<?php echo $sort_by; ?>&sort_order=<?php echo $sort_order; ?>&search_dormitory=<?php echo $search_dormitory; ?>&search_gender=<?php echo $search_gender; ?>&search_name=<?php echo $search_name; ?>&search_department=<?php echo $search_department; ?>&search_college=<?php echo $search_college; ?>"
                                aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <div class="card-body">
            <!-- Print Button -->
            <button class="btn btn-primary mb-3" onclick="printTable()">Print Table</button>
            <button class="btn btn-primary mb-3" onclick="printVisibleTableData()">Print All Table</button>

            <!-- Assignment Table -->
            <?php if (empty($assignments)): ?>
                <p>No assignments found.</p>
            <?php else: ?>
                <table id="assignmentsTable" class="table table-striped">
                <thead>
                        <tr>
                            <th>Assignment ID</th>
                            <th>Student Name</th>
                            <th>Username</th>
                            <th>Gender</th>
                            <th>Department</th>
                            <th>College</th>
                            <th>College Type</th>
                            <th>Dormitory</th>
                            <th>Block</th>
                            <th>Room Number</th>
                            <th>Bed Number</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><?php echo $assignment['assignment_id']; ?></td>
                                <td><?php echo $assignment['name']; ?></td>
                                <td><?php echo $assignment['username']; ?></td>
                                <td><?php echo $assignment['gender']; ?></td>
                                <td><?php echo $assignment['department_name']; ?></td>
                                <td><?php echo $assignment['college_name']; ?></td>
                                <td><?php echo $assignment['college_type']; ?></td>
                                <td><?php echo $assignment['dormitory_name']; ?></td>
                                <td><?php echo $assignment['block_name']; ?></td>
                                <td><?php echo $assignment['room_number']; ?></td>
                                <td><?php echo $assignment['bed_number']; ?></td>
                                <td><?php echo $assignment['assignment_start_date']; ?></td>
                                <td><?php echo $assignment['assignment_end_date']; ?></td>
                            </tr>
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
    function printVisibleTableData() {
        var printContents = "<table>" + document.getElementById("assignmentsTable").innerHTML + "</table>";
        var visibleRows = document.querySelectorAll("#assignmentsTable tbody tr");
        var originalDisplay = [];
        
        // Hide non-visible rows temporarily
        visibleRows.forEach(function(row) {
            if (row.style.display === "none") {
                originalDisplay.push(row);
                row.style.display = "";
            }
        });

        // Print the visible table data
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        
        // Restore original display
        originalDisplay.forEach(function(row) {
            row.style.display = "none";
        });

        // Restore original body content
        document.body.innerHTML = originalContents;
    }
</script>

