-- e_learning.sql (Combined, ready to run)
-- Creates database, tables, sample admin/student, 4 courses and 100 quiz questions (25 per course)

CREATE DATABASE IF NOT EXISTS e_learning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE e_learning;

-- Drop tables if they exist to allow re-running this script without FK errors
DROP TABLE IF EXISTS results;
DROP TABLE IF EXISTS quizzes;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS courses;

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  short_desc VARCHAR(255),
  content TEXT,
  image VARCHAR(255),
  video VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Students table
CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE,
  password VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quizzes table (references courses)
CREATE TABLE IF NOT EXISTS quizzes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  question TEXT,
  opt1 VARCHAR(255),
  opt2 VARCHAR(255),
  opt3 VARCHAR(255),
  opt4 VARCHAR(255),
  answer_key TINYINT,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Results table (references students and courses)
CREATE TABLE IF NOT EXISTS results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  course_id INT,
  score INT,
  taken_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sample admin
INSERT INTO admins (username, password) VALUES ('admin','admin123');

-- sample student
INSERT INTO students (name, email, password) VALUES ('Raja','student1@example.com','student123');

-- sample courses (4)
INSERT INTO courses (title, short_desc, content, image, video) VALUES
('Web Development Basics','HTML, CSS, JS intro','Intro content for Web Development Basics.','images/web.jpg',''),
('Database Fundamentals','SQL, normalization, queries','Intro content for Database Fundamentals.','images/db.jpg',''),
('Programming in C','C language basics and practice','Intro content for Programming in C.','images/c.jpg',''),
('Computer Networks','Intro to networking concepts','Intro content for Computer Networks.','images/net.jpg','');

-- QUIZ INSERTS (100 rows: 25 per course)

-- COURSE 1: Web Development Basics (course_id = 1)
INSERT INTO quizzes (course_id, question, opt1, opt2, opt3, opt4, answer_key) VALUES
(1, 'Which tag creates a paragraph?', '<p>', '<div>', '<span>', '<h1>', 1),
(1, 'Which attribute adds a link in anchor tag?', 'src', 'href', 'alt', 'id', 2),
(1, 'HTML stands for?', 'Hyperlinks Text Markup', 'Hyper Text Markup Language', 'Home Tool Markup Language', 'Hyperlinking Text Markup Language', 2),
(1, 'Which tag is used for an ordered list?', '<ul>', '<ol>', '<li>', '<list>', 2),
(1, 'Which CSS property changes text color?', 'background-color', 'font-size', 'color', 'margin', 3),
(1, 'How do you add an external CSS file?', '<link rel="stylesheet">', '<script src=>', '<style src=>', '<css src=>', 1),
(1, 'Which element contains JavaScript code?', '<js>', '<script>', '<code>', '<javascript>', 2),
(1, 'How to select an element by id in CSS?', '.myclass', '#myid', 'div', '*', 2),
(1, 'Which HTML tag is used for images?', '<img>', '<image>', '<pic>', '<photo>', 1),
(1, 'Which attribute provides alternate text for an image?', 'alt', 'src', 'title', 'name', 1),
(1, 'How to comment in HTML?', '// comment', '/* comment */', '<!-- comment -->', '# comment', 3),
(1, 'CSS box model includes which property?', 'border', 'database', 'function', 'query', 1),
(1, 'Which property sets element width?', 'height', 'width', 'font', 'display', 2),
(1, 'How to make text bold in HTML?', '<b>', '<bold>', '<strong>', 'both 1 and 3', 4),
(1, 'Which JavaScript method shows a message box?', 'console.log()', 'alert()', 'prompt()', 'show()', 2),
(1, 'Which selector selects all paragraphs?', 'p', '.p', '#p', '*p', 1),
(1, 'Which CSS display value shows element as block?', 'inline', 'block', 'float', 'flex', 2),
(1, 'HTML form submit method using visible data is?', 'GET', 'POST', 'PUT', 'DELETE', 1),
(1, 'Which input type is used for email?', 'text', 'email', 'mail', 'address', 2),
(1, 'Which tag defines a table row?', '<td>', '<tr>', '<th>', '<table>', 2),
(1, 'In CSS how to set padding?', 'padding', 'pad', 'space', 'margin', 1),
(1, 'Which JavaScript operator checks equality of value only?', '===', '!==', '==', '=', 3),
(1, 'Which tag is for the document title?', '<meta>', '<title>', '<head>', '<header>', 2),
(1, 'To include JavaScript file use which tag?', '<script src="file.js">', '<link href="file.js">', '<js src="file.js">', '<include src="file.js">', 1),
(1, 'Which CSS unit is relative to font size?', 'px', 'em', 'cm', 'in', 2);

-- COURSE 2: Database Fundamentals (course_id = 2)
INSERT INTO quizzes (course_id, question, opt1, opt2, opt3, opt4, answer_key) VALUES
(2, 'Which language is used to manage relational databases?', 'HTML', 'CSS', 'SQL', 'JS', 3),
(2, 'Which SQL command retrieves data?', 'INSERT', 'UPDATE', 'DELETE', 'SELECT', 4),
(2, 'Which SQL command removes rows?', 'DROP', 'DELETE', 'TRUNCATE', 'REMOVE', 2),
(2, 'Which keyword sorts query results?', 'ORDER BY', 'GROUP BY', 'SORT', 'FILTER', 1),
(2, 'What is a primary key?', 'Unique identifier for table rows', 'Duplicate field', 'Non indexed column', 'Foreign table', 1),
(2, 'Which normal form avoids repeating groups?', '1NF', '2NF', '3NF', '4NF', 1),
(2, 'Which command adds a new table?', 'CREATE TABLE', 'ADD TABLE', 'NEW TABLE', 'MAKE TABLE', 1),
(2, 'Which SQL command changes table structure?', 'ALTER TABLE', 'UPDATE TABLE', 'MODIFY TABLE', 'CHANGE TABLE', 1),
(2, 'Which join returns only matching rows?', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'FULL JOIN', 3),
(2, 'Which SQL clause filters groups?', 'WHERE', 'HAVING', 'GROUP BY', 'ORDER BY', 2),
(2, 'Which datatype stores text?', 'INT', 'VARCHAR', 'DATE', 'BOOL', 2),
(2, 'Which command adds a new column?', 'ADD COLUMN', 'ALTER COLUMN', 'ALTER TABLE ADD COLUMN', 'NEW COLUMN', 3),
(2, 'Which function counts rows?', 'SUM()', 'COUNT()', 'AVG()', 'TOTAL()', 2),
(2, 'Which index speeds up search?', 'KEY', 'INDEX', 'SORT', 'MAP', 2),
(2, 'What does ACID stand for?', 'Atomicity Consistency Isolation Durability', 'Access Control Is Data', 'All Commands In DB', 'None', 1),
(2, 'Which SQL removes a table and data?', 'DROP TABLE', 'DELETE TABLE', 'TRUNCATE TABLE', 'REMOVE TABLE', 1),
(2, 'Which constraint enforces unique values?', 'NOT NULL', 'UNIQUE', 'CHECK', 'DEFAULT', 2),
(2, 'Which is a relational DBMS?', 'MySQL', 'HTML', 'CSS', 'JS', 1),
(2, 'Which command inserts a record?', 'INSERT INTO', 'ADD INTO', 'CREATE ROW', 'NEW ENTRY', 1),
(2, 'Which datatype is for date values?', 'VARCHAR', 'DATE', 'INT', 'TEXT', 2),
(2, 'Which SQL aggregates average?', 'TOTAL()', 'AVG()', 'MEAN()', 'SUM()', 2),
(2, 'Which clause groups rows?', 'ORDER BY', 'GROUP BY', 'HAVING', 'WHERE', 2),
(2, 'Which is used for referential integrity?', 'PRIMARY KEY', 'FOREIGN KEY', 'UNIQUE', 'CHECK', 2),
(2, 'Which operation combines rows from two tables?', 'UNION', 'INTERSECT', 'JOIN', 'MERGE', 3),
(2, 'Which SQL returns top N rows in MySQL?', 'TOP', 'LIMIT', 'FIRST', 'ROWNUM', 2);

-- COURSE 3: Programming in C (course_id = 3)
INSERT INTO quizzes (course_id, question, opt1, opt2, opt3, opt4, answer_key) VALUES
(3, 'Which file is header file for standard IO?', 'stdio.h', 'stdlib.h', 'string.h', 'math.h', 1),
(3, 'Which function is the entry point in C?', 'start()', 'main()', 'init()', 'run()', 2),
(3, 'Which operator is used for address of variable?', '*', '&', '->', '.', 2),
(3, 'Which data type stores whole numbers?', 'float', 'double', 'int', 'char', 3),
(3, 'Which format specifier for integer in printf?', '%f', '%d', '%c', '%s', 2),
(3, 'Which loop executes at least once before condition check?', 'for', 'while', 'do while', 'foreach', 3),
(3, 'Which function allocates memory dynamically?', 'malloc', 'alloc', 'new', 'create', 1),
(3, 'Which header is for memory functions?', 'memory.h', 'stdlib.h', 'string.h', 'malloc.h', 2),
(3, 'Which operator gets value pointed by pointer?', '&', '*', '%', '@', 2),
(3, 'Which statement stops loop execution?', 'stop', 'break', 'exit', 'continue', 2),
(3, 'Which specifier for character in printf?', '%c', '%s', '%d', '%f', 1),
(3, 'Which function compares two strings?', 'strcomp', 'strcmp', 'compare', 'strcpy', 2),
(3, 'Which function copies a string?', 'strcpy', 'strcat', 'strcmp', 'strlen', 1),
(3, 'Which returns length of string?', 'strlen', 'size', 'length', 'count', 1),
(3, 'Which header for string functions?', 'strings.h', 'string.h', 'str.h', 'cstring', 2),
(3, 'Which symbol ends a statement?', '.', ',', ';', '::', 3),
(3, 'Which operator is remainder operator?', '/', '*', '%', '//', 3),
(3, 'Which keyword declares a macro constant?', 'var', 'const', '#define', 'static', 3),
(3, 'Which function frees allocated memory?', 'free', 'delete', 'release', 'dispose', 1),
(3, 'Which is correct single-line comment in C?', '/* comment */', '// comment', '# comment', '-- comment', 2),
(3, 'Which type is used for floating point numbers?', 'int', 'char', 'float', 'bool', 3),
(3, 'Which header for math functions?', 'math.h', 'cmath', 'mth.h', 'maths.h', 1),
(3, 'Which function reads formatted input?', 'scanf', 'read', 'input', 'gets', 1),
(3, 'Which operator is used for assignment?', '==', '>=', '=', '!=', 3),
(3, 'Which loop is best when iteration count known?', 'while', 'do while', 'for', 'if', 3);

-- COURSE 4: Computer Networks (course_id = 4)
INSERT INTO quizzes (course_id, question, opt1, opt2, opt3, opt4, answer_key) VALUES
(4, 'What is the layer that routes packets?', 'Application', 'Transport', 'Network', 'Data Link', 3),
(4, 'Which protocol provides reliable transport?', 'UDP', 'TCP', 'ICMP', 'ARP', 2),
(4, 'What does IP stand for?', 'Internet Protocol', 'Internal Protocol', 'Interface Protocol', 'Internet Process', 1),
(4, 'Which device connects networks at layer 2?', 'Router', 'Switch', 'Modem', 'Repeater', 2),
(4, 'Which address is 32 bit in IPv4?', 'MAC address', 'IP address', 'Port number', 'SSID', 2),
(4, 'Which protocol resolves IP to MAC?', 'DNS', 'DHCP', 'ARP', 'FTP', 3),
(4, 'Which layer provides end to end communication?', 'Physical', 'Data Link', 'Transport', 'Network', 3),
(4, 'Which port is used for HTTP?', '443', '21', '80', '25', 3),
(4, 'Which protocol resolves domain names?', 'DHCP', 'DNS', 'SMTP', 'FTP', 2),
(4, 'Which layer converts bits to signals?', 'Application', 'Physical', 'Session', 'Transport', 2),
(4, 'Which device amplifies signal?', 'Router', 'Switch', 'Repeater', 'Bridge', 3),
(4, 'Which protocol is used for email send?', 'POP3', 'IMAP', 'SMTP', 'HTTP', 3),
(4, 'Which is connectionless protocol?', 'TCP', 'UDP', 'FTP', 'SSH', 2),
(4, 'Which protocol assigns IP automatically?', 'DNS', 'DHCP', 'HTTP', 'ARP', 2),
(4, 'Which device separates collision domains?', 'Hub', 'Switch', 'Bridge', 'Modem', 3),
(4, 'Which is a wireless security protocol?', 'WEP', 'WPA2', 'SSL', 'TLS', 2),
(4, 'Which layer provides encryption for web?', 'Network', 'Transport', 'Application', 'Presentation', 2),
(4, 'Which addresses are used on layer 2?', 'IP addresses', 'MAC addresses', 'Port numbers', 'URLs', 2),
(4, 'Which model has 7 layers?', 'TCP IP', 'OSIRM', 'OSI', 'Ethernet', 3),
(4, 'Which protocol reports errors in IP networks?', 'ICMP', 'IGMP', 'ARP', 'DNS', 1),
(4, 'Which protocol for file transfer?', 'HTTP', 'FTP', 'SMTP', 'SNMP', 2),
(4, 'Which is used to monitor network devices?', 'SSH', 'SNMP', 'SMTP', 'POP3', 2),
(4, 'Which port is used for HTTPS?', '80', '21', '443', '25', 3),
(4, 'Which technique divides network into subnets?', 'Masking', 'Subnetting', 'Routing', 'Bridging', 2),
(4, 'Which protocol for secure remote login?', 'FTP', 'TELNET', 'SSH', 'HTTP', 3);
