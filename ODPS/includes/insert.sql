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