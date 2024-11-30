<?php
// Description: File for establishing database connection.
// Functionality: Contains code to connect to the database and provides connection object for database operations.

// Database connection parameters
$host = "localhost"; // Change to your host name if necessary
$username = "root"; // Change to your database username if necessary
$password = ""; // Change to your database password if necessary
$database = "odps"; // Change to your desired database name

// Create database connection
$conn = new mysqli($host, $username, $password);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$create_db_query = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($create_db_query) !== TRUE) {
    echo "Error creating database: " . $conn->error . "\n";
}

// Select the database
$conn->select_db($database);

// Create tables if they do not exist
$create_admins_table = "
CREATE TABLE IF NOT EXISTS admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL
)";
$conn->query($create_admins_table);

// Add default admin if not available
$check_admin_query = "SELECT * FROM admin WHERE username = ?";
$stmt = $conn->prepare($check_admin_query);
$stmt->bind_param("s", $default_admin_username);
$default_admin_username = "adem";
$stmt->execute();
$admin_result = $stmt->get_result();

if ($admin_result->num_rows == 0) {
    $default_admin_password = password_hash("adem123", PASSWORD_DEFAULT);
    $default_admin_name = "Adem Abdrei";
    $default_admin_contact_number = "0923365046";

    $add_default_admin_query = "INSERT INTO admin (username, password, name, contact_number) 
                                VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($add_default_admin_query);
    $stmt->bind_param("ssss", $default_admin_username, $default_admin_password, $default_admin_name, $default_admin_contact_number);
    $stmt->execute();
}

// Create tables if they do not exist
$create_System_admins_table = "
CREATE TABLE IF NOT EXISTS system_admin (
    system_admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL
)";
$conn->query($create_System_admins_table);

// Add default admin if not available
$check_system_admin_query = "SELECT * FROM system_admin WHERE username = ?";
$stmt = $conn->prepare($check_system_admin_query);
$stmt->bind_param("s", $default_system_admin_username);
$default_system_admin_username = "adem";
$stmt->execute();
$system_admin_result = $stmt->get_result();

if ($system_admin_result->num_rows == 0) {
    $default_system_admin_password = password_hash("adem123", PASSWORD_DEFAULT);
    $default_system_admin_name = "Adem Abdrei";
    $default_system_admin_contact_number = "0923365046";

    $add_default_system_admin_query = "INSERT INTO system_admin (username, password, name, contact_number) 
                                VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($add_default_system_admin_query);
    $stmt->bind_param("ssss", $default_system_admin_username, $default_system_admin_password, $default_system_admin_name, $default_system_admin_contact_number);
    $stmt->execute();
}
$table_creation_queries = [
    // Table to store admin
    "CREATE TABLE IF NOT EXISTS admin (
        admin_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        contact_number VARCHAR(15) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS system_admin (
        system_admin_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        contact_number VARCHAR(15) NOT NULL
    )",


    // Table to store dormitories
    "CREATE TABLE IF NOT EXISTS dormitories (
        dormitory_id INT AUTO_INCREMENT PRIMARY KEY,
        dormitory_name VARCHAR(100) NOT NULL,
        dormitory_type ENUM('FBE Male', 'FBE Female', 'Main Male', 'Main Female') NOT NULL,    
        dormitory_location VARCHAR(255),
        dormitory_capacity INT NOT NULL
    )",

    // Table to store managers
    "CREATE TABLE IF NOT EXISTS manager (
        manager_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        contact_number VARCHAR(15) NOT NULL,
        dormitory_id INT NOT NULL,
        FOREIGN KEY (dormitory_id) REFERENCES dormitories(dormitory_id)
    )",



    // Table to store blocks
    "CREATE TABLE IF NOT EXISTS blocks (
        block_id INT AUTO_INCREMENT PRIMARY KEY,
        block_name VARCHAR(100) NOT NULL,
        block_capacity INT NOT NULL,
        dormitory_id INT,
        FOREIGN KEY (dormitory_id) REFERENCES dormitories(dormitory_id)
    )",


    // Table to store proctors
    "CREATE TABLE IF NOT EXISTS proctor (
        proctor_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        contact_number VARCHAR(15) NOT NULL,
        block_id INT NOT NULL,
        FOREIGN KEY (block_id) REFERENCES blocks(block_id)
    )",

    // Table to store colleges
    "CREATE TABLE IF NOT EXISTS colleges (
        college_id INT AUTO_INCREMENT PRIMARY KEY,
        college_type ENUM('Natural', 'Social') NOT NULL,
        college_name VARCHAR(100) NOT NULL
    )",

    // Table to store departments
    "CREATE TABLE IF NOT EXISTS departments (
        department_id INT AUTO_INCREMENT PRIMARY KEY,
        department_name VARCHAR(100) NOT NULL,
        college_id INT,
        FOREIGN KEY (college_id) REFERENCES colleges(college_id)
    )",

    // Table to store students
    "CREATE TABLE IF NOT EXISTS student (
        student_id INT AUTO_INCREMENT PRIMARY KEY,
        department_id INT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        contact_number VARCHAR(15) NOT NULL,
        gender ENUM('Male', 'Female') NOT NULL,
        batch ENUM('1', '2','3','4') NOT NULL,
        FOREIGN KEY (department_id) REFERENCES departments(department_id)
    )",


    // Table to store rooms
    "CREATE TABLE IF NOT EXISTS rooms (
        room_id INT AUTO_INCREMENT PRIMARY KEY,
        room_number VARCHAR(10) NOT NULL,
        block_id INT,
        room_description TEXT,
        room_facilities TEXT,
        room_capacity INT NOT NULL,
        room_availability TINYINT(1),
        FOREIGN KEY (block_id) REFERENCES blocks(block_id)
    )",

    // Table to store beds
    "CREATE TABLE IF NOT EXISTS beds (
        bed_id INT AUTO_INCREMENT PRIMARY KEY,
        room_id INT,
        bed_number VARCHAR(10) NOT NULL,
        student_id INT,
        FOREIGN KEY (room_id) REFERENCES rooms(room_id),
        FOREIGN KEY (student_id) REFERENCES student(student_id)
    )",

    // Table to store dormitory assignments
    "CREATE TABLE IF NOT EXISTS dormitory_assignments (
        assignment_id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        bed_id INT,
        assignment_start_date DATE,
        assignment_end_date DATE,
        FOREIGN KEY (student_id) REFERENCES student(student_id),
        FOREIGN KEY (bed_id) REFERENCES beds(bed_id)
    )",

    // Table to store notices
    "CREATE TABLE IF NOT EXISTS notices (
        notice_id INT AUTO_INCREMENT PRIMARY KEY,
        notice_title VARCHAR(255) NOT NULL,
        notice_content TEXT,
        notice_images TEXT,
        notice_posted_by VARCHAR(100),
        notice_posted_role VARCHAR(100),
        notice_posted_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    // Table to store comments
    "CREATE TABLE IF NOT EXISTS comments (
        comment_id INT AUTO_INCREMENT PRIMARY KEY,
        notice_id INT,
        student_id INT,
        comment_content TEXT,
        comment_posted_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (notice_id) REFERENCES notices(notice_id),
        FOREIGN KEY (student_id) REFERENCES student(student_id)
    )"
];

// Execute table creation queries
foreach ($table_creation_queries as $query) {
    $conn->query($query);
}
