<?php
session_start(); require 'db_connect.php';
$r=$conn->query("SELECT * FROM courses ORDER BY id ASC");
$courses=$r?$r->fetch_all(MYSQLI_ASSOC):[];
$logged=isset($_SESSION['student_id']);
?>
<!doctype html><html><head>
<title>Courses - Bright Future</title>
<link rel="stylesheet" href="styles.css">
<style>
body{margin:0;font-family:sans-serif;
background:linear-gradient(135deg,#7b3fe4,#ec4899)}
.login-banner{background:#fff8e6;border-left:4px solid #f59e0b;
padding:12px;border-radius:6px;margin:12px 0}
.courses-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
gap:18px;margin-top:16px}
.course-card{background:#fff;padding:12px;border-radius:8px;
box-shadow:0 6px 18px #0002}
.course-card img{width:100%;height:140px;object-fit:cover;border-radius:8px}
.btn{display:inline-block;padding:8px 12px;border-radius:8px;
background:linear-gradient(90deg,#7b3fe4,#6f2bd8);
color:#fff;text-decoration:none;font-weight:700}
.back{
position:fixed;top:20px;right:20px;
background:#fff;padding:8px 14px;
border-radius:20px;text-decoration:none;
color:#7b3fe4;font-weight:700;
box-shadow:0 4px 12px #0002
}
</style></head><body>

<a href="index.php" class="back">← Back</a>

<header class="site-header">
<div class="brand">BRIGHT FUTURE</div>
<nav>
<a href="index.php">Home</a>
<a href="courses.php">Courses</a>
<a href="student_login.php">Student Login</a>
<a href="admin_login.php">Admin Login</a>
</nav>
</header>

<main class="container">
<h2>Available Courses</h2>

<?php if(!$logged): ?>
<div class="login-banner">
Please login to open and attend courses.
</div>
<?php endif; ?>

<div class="courses-grid">
<?php if(!$courses): ?><p>No courses found.</p>
<?php else: foreach($courses as $c):
$cid=(int)$c['id'];
$link=$logged?"course.php?id=$cid":
"student_login.php?login_required=1&next=course.php?id=$cid";
?>
<div class="course-card">
<?php if($c['image'] && file_exists($c['image'])): ?>
<img src="<?=htmlspecialchars($c['image'])?>">
<?php else: ?>
<div style="height:140px;background:#f3f4f6;border-radius:8px;
display:flex;align-items:center;justify-content:center;color:#9aa1a8">
No image</div>
<?php endif; ?>

<h3><?=htmlspecialchars($c['title'])?></h3>
<p><?=htmlspecialchars($c['short_desc'])?></p>
<a class="btn" href="<?=htmlspecialchars($link)?>">Open Course</a>
</div>
<?php endforeach; endif; ?>
</div>
</main>
</body></html>
