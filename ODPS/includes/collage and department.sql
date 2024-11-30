
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















