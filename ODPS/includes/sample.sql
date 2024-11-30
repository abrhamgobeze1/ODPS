INSERT INTO `dormitories` (`dormitory_name`, `dormitory_type`, `dormitory_location`, `dormitory_capacity`) VALUES
('Main Dormitory For Male', 'Main Male', 'Wallage University Main Dormitory For Male', 30),
('Main Dormitory For Female', 'Main Female', 'Wallage University Main Dormitory For Female', 30),
('FBE Dormitory For Male', 'FBE Male', 'Wallage University FBE Dormitory For Male', 30),
('FBE Dormitory For Female', 'FBE Female', 'Wallage University FBE Dormitory For Female', 30);



-- Insert blocks for dormitory_id 1
INSERT INTO blocks (block_name, block_capacity, dormitory_id)
VALUES
('Block 1', 30, 1),
('Block 2', 30, 1),
('Block 3', 30, 1),
('Block 4', 30, 1),
('Block 5', 30, 1),
('Block 6', 30, 1),
('Block 7', 30, 1),
('Block 8', 30, 1),
('Block 9', 30, 1),
('Block 10', 30, 1),
('Block 11', 30, 1),
('Block 12', 30, 1),
('Block 13', 30, 1),
('Block 14', 30, 1),
('Block 15', 30, 1),
('Block 16', 30, 1),
('Block 17', 30, 1),
('Block 18', 30, 1),
('Block 19', 30, 1),
('Block 20', 30, 1),
('Block 21', 30, 1),
('Block 22', 30, 1),
('Block 23', 30, 1),
('Block 24', 30, 1),
('Block 25', 30, 1),
('Block 26', 30, 1),
('Block 27', 30, 1),
('Block 28', 30, 1),
('Block 29', 30, 1),
('Block 30', 30, 1);

-- Insert blocks for dormitory_id 2
INSERT INTO blocks (block_name, block_capacity, dormitory_id)
VALUES
('Block 31', 30, 2),
('Block 32', 30, 2),
('Block 33', 30, 2),
('Block 34', 30, 2),
('Block 35', 30, 2),
('Block 36', 30, 2),
('Block 37', 30, 2),
('Block 38', 30, 2),
('Block 39', 30, 2),
('Block 40', 30, 2),
('Block 41', 30, 2),
('Block 42', 30, 2),
('Block 43', 30, 2),
('Block 44', 30, 2),
('Block 45', 30, 2),
('Block 46', 30, 2),
('Block 47', 30, 2),
('Block 48', 30, 2),
('Block 49', 30, 2),
('Block 50', 30, 2),
('Block 51', 30, 2),
('Block 52', 30, 2),
('Block 53', 30, 2),
('Block 54', 30, 2),
('Block 55', 30, 2),
('Block 56', 30, 2),
('Block 57', 30, 2),
('Block 58', 30, 2),
('Block 59', 30, 2),
('Block 60', 30, 2);

-- Insert blocks for dormitory_id 3
INSERT INTO blocks (block_name, block_capacity, dormitory_id)
VALUES
('Block 61', 30, 3),
('Block 62', 30, 3),
('Block 63', 30, 3),
('Block 64', 30, 3),
('Block 65', 30, 3),
('Block 66', 30, 3),
('Block 67', 30, 3),
('Block 68', 30, 3),
('Block 69', 30, 3),
('Block 70', 30, 3),
('Block 71', 30, 3),
('Block 72', 30, 3),
('Block 73', 30, 3),
('Block 74', 30, 3),
('Block 75', 30, 3),
('Block 76', 30, 3),
('Block 77', 30, 3),
('Block 78', 30, 3),
('Block 79', 30, 3),
('Block 80', 30, 3),
('Block 81', 30, 3),
('Block 82', 30, 3),
('Block 83', 30, 3),
('Block 84', 30, 3),
('Block 85', 30, 3),
('Block 86', 30, 3),
('Block 87', 30, 3),
('Block 88', 30, 3),
('Block 89', 30, 3),
('Block 90', 30, 3);

-- Insert blocks for dormitory_id 4
INSERT INTO blocks (block_name, block_capacity, dormitory_id)
VALUES
('Block 91', 30, 4),
('Block 92', 30, 4),
('Block 93', 30, 4),
('Block 94', 30, 4),
('Block 95', 30, 4),
('Block 96', 30, 4),
('Block 97', 30, 4),
('Block 98', 30, 4),
('Block 99', 30, 4),
('Block 100', 30, 4),
('Block 101', 30, 4),
('Block 102', 30, 4),
('Block 103', 30, 4),
('Block 104', 30, 4),
('Block 105', 30, 4),
('Block 106', 30, 4),
('Block 107', 30, 4),
('Block 108', 30, 4),
('Block 109', 30, 4),
('Block 110', 30, 4),
('Block 111', 30, 4),
('Block 112', 30, 4),
('Block 113', 30, 4),
('Block 114', 30, 4),
('Block 115', 30, 4),
('Block 116', 30, 4),
('Block 117', 30, 4),
('Block 118', 30, 4),
('Block 119', 30, 4),
('Block 120', 30, 4);








-- Insert rooms
INSERT INTO rooms (room_number, block_id, room_capacity, room_availability)
SELECT CONCAT('Room ', room_num), b.block_id, 4, 1
FROM (SELECT 1 AS room_num UNION ALL
     SELECT 2 UNION ALL
     SELECT 3 UNION ALL
     SELECT 4 UNION ALL
     SELECT 5 UNION ALL
     SELECT 6 UNION ALL
     SELECT 7 UNION ALL
     SELECT 8 UNION ALL
     SELECT 9 UNION ALL
     SELECT 10 UNION ALL
     SELECT 11 UNION ALL
     SELECT 12 UNION ALL
     SELECT 13 UNION ALL
     SELECT 14 UNION ALL
     SELECT 15 UNION ALL
     SELECT 16 UNION ALL
     SELECT 17 UNION ALL
     SELECT 18 UNION ALL
     SELECT 19 UNION ALL
     SELECT 20 UNION ALL
     SELECT 21 UNION ALL
     SELECT 22 UNION ALL
     SELECT 23 UNION ALL
     SELECT 24 UNION ALL
     SELECT 25 UNION ALL
     SELECT 26 UNION ALL
     SELECT 27 UNION ALL
     SELECT 28 UNION ALL
     SELECT 29 UNION ALL
     SELECT 30) AS room_numbers
CROSS JOIN blocks b
ORDER BY b.block_id, room_num;









INSERT INTO beds (room_id, bed_number, student_id)
SELECT
  FLOOR((bed_num - 1) / 4) + 1 AS room_id,
  CONCAT('Bed ', LPAD(bed_num, 5, '0')) AS bed_number,
  NULL AS student_id
FROM (
  SELECT 
    ROW_NUMBER() OVER (ORDER BY NULL) AS bed_num
  FROM information_schema.tables t1
  CROSS JOIN information_schema.tables t2
  LIMIT 14400
) t;



















INSERT INTO colleges (college_type, college_name) VALUES
('Natural', 'College of Engineering and Technology'),
('Social', 'College of Arts and Sciences'),
('Social', 'College of Business'),
('Social', 'College of Education'),
('Natural', 'College of Health Sciences'),
('Social', 'College of Law'),
('Natural', 'College of Agriculture'),
('Social', 'College of Social Sciences'),
('Natural', 'College of Natural Freshman'),
('Social', 'College of Social Freshman');
















-- Insert 10 departments for colleges with ID 1 to 8
INSERT INTO departments (department_name, college_id)
VALUES
    ('Department of Computer Science', 1),
    ('Department of Electrical Engineering', 1),
    ('Department of Mechanical Engineering', 1),
    ('Department of Civil Engineering', 1),
    ('Department of Chemical Engineering', 1),
    ('Department of Aerospace Engineering', 1),
    ('Department of Biotechnology', 1),
    ('Department of Electronics and Communication', 1),
    ('Department of Information Technology', 1),
    ('Department of Environmental Engineering', 1),
    ('Department of Economics', 2),
    ('Department of Political Science', 2),
    ('Department of Sociology', 2),
    ('Department of Psychology', 2),
    ('Department of History', 2),
    ('Department of English', 2),
    ('Department of Philosophy', 2),
    ('Department of Anthropology', 2),
    ('Department of Geography', 2),
    ('Department of Communication Studies', 2),
    ('Department of Accounting', 3),
    ('Department of Finance', 3),
    ('Department of Management', 3),
    ('Department of Marketing', 3),
    ('Department of Information Systems', 3),
    ('Department of Entrepreneurship', 3),
    ('Department of Business Analytics', 3),
    ('Department of International Business', 3),
    ('Department of Human Resource Management', 3),
    ('Department of Operations Management', 3),
    ('Department of Elementary Education', 4),
    ('Department of Secondary Education', 4),
    ('Department of Educational Leadership', 4),
    ('Department of Special Education', 4),
    ('Department of Educational Psychology', 4),
    ('Department of Curriculum and Instruction', 4),
    ('Department of Educational Technology', 4),
    ('Department of Early Childhood Education', 4),
    ('Department of Adult and Continuing Education', 4),
    ('Department of Educational Policy Studies', 4),
    ('Department of Nursing', 5),
    ('Department of Public Health', 5),
    ('Department of Pharmacy', 5),
    ('Department of Physical Therapy', 5),
    ('Department of Occupational Therapy', 5),
    ('Department of Dentistry', 5),
    ('Department of Medical Laboratory Science', 5),
    ('Department of Health Informatics', 5),
    ('Department of Health Administration', 5),
    ('Department of Biomedical Engineering', 5),
    ('Department of Law', 6),
    ('Department of Criminal Justice', 6),
    ('Department of International Law', 6),
    ('Department of Environmental Law', 6),
    ('Department of Intellectual Property Law', 6),
    ('Department of Business Law', 6),
    ('Department of Constitutional Law', 6),
    ('Department of Family Law', 6),
    ('Department of Labor Law', 6),
    ('Department of Tax Law', 6),
    ('Department of Crop Science', 7),
    ('Department of Animal Science', 7),
    ('Department of Agricultural Economics', 7),
    ('Department of Food Science and Technology', 7),
    ('Department of Horticulture', 7),
    ('Department of Agricultural Engineering', 7),
    ('Department of Plant Pathology', 7),
    ('Department of Soil Science', 7),
    ('Department of Agricultural Extension', 7),
    ('Department of Agribusiness Management', 7),
    ('Department of Anthropology', 8),
    ('Department of Economics', 8),
    ('Department of Geography', 8),
    ('Department of Political Science', 8),
    ('Department of Psychology', 8),
    ('Department of Social Work', 8),
    ('Department of Sociology', 8),
    ('Department of International Studies', 8),
    ('Department of Public Administration', 8),
    ('Department of Urban Studies', 8);

-- Insert 1 department for colleges with ID 9 and 10
INSERT INTO departments (department_name, college_id)
VALUES
    ('Department of Natural Sciences', 9),
    ('Department of Social Sciences', 10);













-- Insert data into student table
INSERT INTO student (department_id, username, password, name, contact_number, gender, batch)
SELECT
  d.department_id,
  CONCAT('male_student_', d.department_id, '_', s.student_num, '_', b.batch),
  'password123',
  CONCAT('Male Student ', d.department_id, '_', s.student_num, '_', b.batch),
  CONCAT('123456789', s.student_num),
  'Male',
  b.batch
FROM (
  SELECT 1 AS student_num UNION ALL
  SELECT 2 UNION ALL
  SELECT 3 UNION ALL
  SELECT 4 UNION ALL
  SELECT 5
) s
CROSS JOIN (
  SELECT department_id FROM departments WHERE department_id BETWEEN 1 AND 80 ORDER BY department_id
) d
CROSS JOIN (
  SELECT '2' AS batch
  UNION ALL
  SELECT '3'
  UNION ALL
  SELECT '4'
) b;

INSERT INTO student (department_id, username, password, name, contact_number, gender, batch)
SELECT
  d.department_id,
  CONCAT('female_student_', d.department_id, '_', s.student_num, '_', b.batch),
  'password123',
  CONCAT('Female Student ', d.department_id, '_', s.student_num, '_', b.batch),
  CONCAT('987654321', s.student_num),
  'Female',
  b.batch
FROM (
  SELECT 1 AS student_num UNION ALL
  SELECT 2 UNION ALL
  SELECT 3 UNION ALL
  SELECT 4 UNION ALL
  SELECT 5
) s
CROSS JOIN (
  SELECT department_id FROM departments WHERE department_id BETWEEN 1 AND 80 ORDER BY department_id
) d
CROSS JOIN (
  SELECT '2' AS batch
  UNION ALL
  SELECT '3'
  UNION ALL
  SELECT '4'
) b;












DELIMITER //

CREATE PROCEDURE insert_freshman_students()
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE department_id INT;
    DECLARE gender ENUM('Male', 'Female');
    DECLARE username VARCHAR(50);
    DECLARE student_password VARCHAR(255);
    DECLARE name VARCHAR(100);
    DECLARE contact_number VARCHAR(15);

    WHILE i <= 2000 DO
        IF i % 2 = 0 THEN
            SET department_id = 81;
            SET gender = 'Female';
        ELSE
            SET department_id = 82;
            SET gender = 'Male';
        END IF;

        SET username = CONCAT('student', i);
        SET student_password = SHA2(CONCAT('password', i), 256);
        SET name = CONCAT('Student', i);
        SET contact_number = CONCAT('123456789', i);

        INSERT INTO student (
            department_id,
            username,
            `password`,
            name,
            contact_number,
            gender,
            batch
        ) VALUES (
            department_id,
            username,
            student_password,
            name,
            contact_number,
            gender,
            '1'
        );

        SET i = i + 1;
    END WHILE;
END//

DELIMITER ;

CALL insert_freshman_students();















