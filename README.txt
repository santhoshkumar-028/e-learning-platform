Bright Future - E-Learning (XAMPP demo)
--------------------------------------
How to run:
1. Start XAMPP and start Apache and MySQL.
2. Copy the extracted folder 'e_learning' to your XAMPP htdocs (Windows: C:\xampp\htdocs\).
3. Open phpMyAdmin (http://localhost/phpmyadmin) and import the file 'e_learning.sql' found in the project root.
4. Go to http://localhost/e_learning/index.php
Demo accounts:
 - Admin: admin / admin123
 - Student: student1@example.com / student123

Notes:
 - Admin panel allows adding courses (optionally upload image and mp4 video) and adding quiz questions.
 - If you upload videos via admin panel they are saved into videos/ folder and shown on course page.
 - For the placeholder images, I added simple SVG files under images/ (you can replace them with jpg/png).
 - This project is intentionally simple for college demo: passwords are plain-text (for demo only). For production, use password_hash and prepared statements.
