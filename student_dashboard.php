<?php
session_start();
if (!isset($_SESSION['student_id'])) { header('Location: student_login.php'); exit; }
require 'db_connect.php';
$res = $conn->query("SELECT * FROM courses");
$name = $_SESSION['student_name'];
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/><title>Dashboard</title><link rel="stylesheet" href="styles.css"></head>
<body>
  <header class="site-header"><div class="brand">BRIGHT FUTURE</div><nav><a href="index.php">Home</a> | <a href="courses.php">Courses</a> | <a href="logout.php">Logout</a></nav></header>
  <main class="container">
    <h2>Welcome, <?php echo htmlspecialchars($name); ?></h2>
    <h3>Your Courses</h3>
    <div class="courses-grid">
      <?php while($r = $res->fetch_assoc()): ?>
        <div class="course-card">
          <img src="<?php echo htmlspecialchars($r['image']); ?>" alt="img"/>
          <h3><?php echo htmlspecialchars($r['title']); ?></h3>
          <p><?php echo htmlspecialchars($r['short_desc']); ?></p>
          <a class="btn" href="course.php?id=<?php echo $r['id']; ?>">Open Course</a>
        </div>
      <?php endwhile; ?>
    </div>
  </main>
</body></html>
<style>
body{
    min-height:100vh;
    background: linear-gradient(135deg, #fdfbfb, #ebedee, #e0f7fa, #fce4ec);
    background-size:300% 300%;
    animation: lightGradient 12s ease infinite;
}

/* Soft moving animation */
@keyframes lightGradient{
    0%{ background-position:0% 50%; }
    50%{ background-position:100% 50%; }
    100%{ background-position:0% 50%; }
}
</style>
