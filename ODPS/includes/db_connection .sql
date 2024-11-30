-- Table for admins users
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL
);

-- Table for manager users
CREATE TABLE managers (
    manager_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL
);

-- Table for proctor users
CREATE TABLE proctors (
    proctor_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL
);

-- Table for colleges
CREATE TABLE colleges (
    college_id INT AUTO_INCREMENT PRIMARY KEY,
    college_type ENUM('Natural', 'Social') NOT NULL;
    college_name VARCHAR(100) NOT NULL
);

-- Table for departments
CREATE TABLE departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    college_id INT,
    FOREIGN KEY (college_id) REFERENCES colleges(college_id)
);

-- Table for students
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
);

-- Table for dormitories
CREATE TABLE dormitories (
    dormitory_id INT AUTO_INCREMENT PRIMARY KEY,
    dormitory_name VARCHAR(100) NOT NULL,
    dormitory_type ENUM('FBE Male', 'FBE Female', 'Main Male', 'Main Female') NOT NULL,     
    dormitory_location VARCHAR(255),
    dormitory_capacity INT NOT NULL
);

-- Table for blocks
CREATE TABLE blocks (
    block_id INT AUTO_INCREMENT PRIMARY KEY,
    block_name VARCHAR(100) NOT NULL,
    dormitory_id INT,
    FOREIGN KEY (dormitory_id) REFERENCES dormitories(dormitory_id)
);

-- Table for rooms
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL,
    block_id INT,
    room_description TEXT,
    room_facilities TEXT,
    room_availability BOOLEAN,
    FOREIGN KEY (block_id) REFERENCES blocks(block_id)
);

-- Table for beds
CREATE TABLE beds (
    bed_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT,
    bed_number VARCHAR(10) NOT NULL,
    student_id INT,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- Table for dormitory assignments
CREATE TABLE dormitory_assignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    bed_id INT,
    assignment_start_date DATE,
    assignment_end_date DATE,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (bed_id) REFERENCES beds(bed_id)
);

-- Table for notices
CREATE TABLE notices (
    notice_id INT AUTO_INCREMENT PRIMARY KEY,
    notice_title VARCHAR(255) NOT NULL,
    notice_content TEXT,
    notice_images TEXT,
    notice_posted_by VARCHAR(100),
    notice_posted_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for comments
CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    notice_id INT,
    student_id INT,
    comment_content TEXT,
    comment_posted_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notice_id) REFERENCES notices(notice_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);
