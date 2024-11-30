admin/
│
├── admin_dashboard.php
│   - Description: This file represents the dashboard for the admin role.
│   - Purpose: Provides an overview of system statistics and access to admin functionalities.
│
├── manage_colleges.php
│   - Description: This file allows the admin to manage colleges within the system.
│   - Purpose: Provides functionalities to add, edit, or delete colleges, including their details and settings.
│
├── manage_departments.php
│   - Description: This file enables the admin to manage departments within colleges.
│   - Purpose: Allows the admin to add, edit, or delete departments, along with their respective settings and configurations.
│
├── manage_students.php
│   - Description: This file provides functionalities for managing student profiles.
│   - Purpose: Allows the admin to add, edit, or delete student information, including dormitory preferences and room assignments.
│
├── manage_rooms.php
│   - Description: This file facilitates the management of dormitory rooms.
│   - Purpose: Allows the admin to add, edit, or delete dormitory rooms, including their types, capacities, and facilities.
│
├── manage_blocks.php
│   - Description: This file handles the management of dormitory blocks.
│   - Purpose: Provides functionalities to add, edit, or delete dormitory blocks, including their locations and capacities.
│
├── generate_reports.php
│   - Description: This file generates reports for administrative purposes.
│   - Purpose: Provides tools to generate various reports, such as occupancy rates, room assignments, and student demographics.
│
├── manage_notices.php
│   - Description: This file allows the admin to manage notices.
│   - Purpose: Provides functionalities to add, edit, or delete notices for students and staff.
│
├── manage_comments.php
│   - Description: This file facilitates the management of comments.
│   - Purpose: Allows the admin to add, edit, or delete comments associated with notices and other system elements.
│
└── ...
    - Description: Placeholder for additional admin functionalities.
manager/
│
├── manager_dashboard.php
│   - Description: This file represents the dashboard for the manager role.
│   - Purpose: Provides an overview of system statistics and access to manager functionalities.
│
├── manage_dormitories.php
│   - Description: This file allows the manager to manage dormitories.
│   - Purpose: Provides functionalities to add, edit, or delete dormitories, including their configurations and settings.
│
├── manage_blocks.php
│   - Description: This file handles the management of dormitory blocks.
│   - Purpose: Provides functionalities to add, edit, or delete dormitory blocks, including their locations and capacities.
│
├── manage_rooms.php
│   - Description: This file facilitates the management of dormitory rooms.
│   - Purpose: Allows the manager to add, edit, or delete dormitory rooms, including their types, capacities, and facilities.
│
├── manage_students.php
│   - Description: This file provides functionalities for managing student profiles.
│   - Purpose: Allows the manager to add, edit, or delete student information, including dormitory preferences and room assignments.
│
├── allocate_rooms.php
│   - Description: This file handles the allocation of rooms.
│   - Purpose: Provides functionalities to allocate rooms to students based on their preferences and availability.
│
├── generate_reports.php
│   - Description: This file generates reports for administrative purposes.
│   - Purpose: Provides tools to generate various reports, such as occupancy rates, room assignments, and student demographics.
│
├── communicate_staff.php
│   - Description: This file enables communication with staff members.
│   - Purpose: Provides a platform for manager-staff communication, including sending notifications and messages.
│
├── manage_notices.php
│   - Description: This file allows the manager to manage notices.
│   - Purpose: Provides functionalities to add, edit, or delete notices for students and staff.
│
├── manage_comments.php
│   - Description: This file facilitates the management of comments.
│   - Purpose: Allows the manager to add, edit, or delete comments associated with notices and other system elements.
│
└── ...
    - Description: Placeholder for additional manager functionalities.
proctor/
│
├── proctor_dashboard.php
│   - Description: This file represents the dashboard for the proctor role.
│   - Purpose: Provides an overview of system statistics and access to proctor functionalities.
│
├── view_student_profiles.php
│   - Description: This file allows the proctor to view student profiles.
│   - Purpose: Provides functionalities to view student information, including dormitory preferences and room assignments.
│
├── view_dormitory_applications.php
│   - Description: This file enables the proctor to view dormitory applications.
│   - Purpose: Allows the proctor to view and manage dormitory accommodation applications submitted by students.
│
├── allocate_rooms.php
│   - Description: This file handles the allocation of rooms.
│   - Purpose: Provides functionalities to allocate rooms to students based on their preferences and availability.
│
├── manage_roommates.php
│   - Description: This file facilitates the management of roommates.
│   - Purpose: Allows the proctor to manage roommate assignments and configurations.
│
├── communicate_students.php
│   - Description: This file enables communication with students.
│   - Purpose: Provides a platform for proctor-student communication, including sending notifications and messages.
│
└── ...
    - Description: Placeholder for additional proctor functionalities.
student/
│
├── student_dashboard.php
│   - Description: This file represents the dashboard for the student role.
│   - Purpose: Provides an overview of student-specific information and access to student functionalities.
│
├── apply_dormitory.php
│   - Description: This file allows the student to apply for dormitory accommodations.
│   - Purpose: Provides functionalities for students to submit dormitory accommodation preferences and applications.
│
├── view_room_assignment.php
│   - Description: This file enables the student to view room assignments.
│   - Purpose: Allows the student to view their assigned dormitory room and roommate information.
│
├── view_roommates.php
│   - Description: This file allows the student to view their roommates.
│   - Purpose: Provides functionalities for students to view information about their assigned roommates.
│
├── view_dormitory_info.php
│   - Description: This file enables the student to view dormitory information.
│   - Purpose: Allows the student to view information about dormitory facilities, rules, and regulations.
│
├── communicate_admin.php
│   - Description: This file enables communication with the admin.
│   - Purpose: Provides a platform for student-admin communication, including sending notifications and messages.
│
├── view_notices.php
│   - Description: This file allows the student to view notices.
│   - Purpose: Provides functionalities for students to view notices and announcements posted by the admin or manager.
│
├── add_comment.php
│   - Description: This file allows the student to add comments.
│   - Purpose: Enables students to add comments to notices or other system elements.
│
└── ...
    - Description: Placeholder for additional student functionalities.
includes/
│
├── header.php
│   - Description: This file contains the header section of the web pages.
│   - Purpose: Provides consistent header content across all pages for branding and navigation.
│
├── footer.php
│   - Description: This file contains the footer section of the web pages.
│   - Purpose: Provides consistent footer content across all pages for copyright information and links.
│
├── db_connection.php
│   - Description: This file establishes a database connection.
│   - Purpose: Connects the system to the database to retrieve and manipulate data.
│
└── ...
    - Description: Placeholder for additional include files.
css/
│
└── style.css
    - Description: This file contains CSS styles for styling the web pages.
    - Purpose: Defines the visual appearance and layout of the web pages to enhance user experience.
js/
│
└── script.js
    - Description: This file contains JavaScript code for client-side scripting functionalities.
    - Purpose: Implements interactive features and behaviors on the web pages to enhance user interactivity.
images/
│
└── (Directory containing image files)
    - Description: This directory contains image files used in the system.
    - Purpose: Provides visual assets for enhancing the user interface and user experience.
index.php
- Description: This file serves as the main entry point of the system.
- Purpose: Redirects users to the appropriate login page based on their roles.
login.php
- Description: This file represents the login page.
- Purpose: Allows users to authenticate themselves to access the system.
logout.php
- Description: This file handles user logout functionality.
- Purpose: Terminates the user's session and redirects them to the login page.
