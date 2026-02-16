<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$course_id = (int)$_GET['id'];
$msg = "";

/* =============================
   UPDATE COURSE DETAILS
============================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_course') {

    $title = trim($_POST['title']);
    $short = trim($_POST['short_desc']);
    $content = trim($_POST['content']);

    $old = $conn->query("SELECT * FROM courses WHERE id=$course_id")->fetch_assoc();

    $imagePath = $old['image'];
    $videoPath = $old['video'];
    $documentPath = $old['document'];

    if (!empty($_FILES['image']['tmp_name'])) {
        $dir = "uploads/images/";
        if (!is_dir($dir)) mkdir($dir,0755,true);
        $name = time()."_".$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'],$dir.$name);
        $imagePath = $dir.$name;
    }

    if (!empty($_FILES['video']['tmp_name'])) {
        $dir = "uploads/videos/";
        if (!is_dir($dir)) mkdir($dir,0755,true);
        $name = time()."_".$_FILES['video']['name'];
        move_uploaded_file($_FILES['video']['tmp_name'],$dir.$name);
        $videoPath = $dir.$name;
    }

    if (!empty($_FILES['document']['tmp_name'])) {
        $dir = "uploads/documents/";
        if (!is_dir($dir)) mkdir($dir,0755,true);
        $name = time()."_".$_FILES['document']['name'];
        move_uploaded_file($_FILES['document']['tmp_name'],$dir.$name);
        $documentPath = $dir.$name;
    }

    $stmt = $conn->prepare("UPDATE courses SET title=?, short_desc=?, content=?, image=?, video=?, document=? WHERE id=?");
    $stmt->bind_param("ssssssi",$title,$short,$content,$imagePath,$videoPath,$documentPath,$course_id);
    $stmt->execute();

    $msg = "Course Updated Successfully!";
}

/* =============================
   UPDATE QUIZ
============================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'update_quiz') {

        $qid = (int)$_POST['quiz_id'];
        $q = trim($_POST['question']);
        $o1 = trim($_POST['opt1']);
        $o2 = trim($_POST['opt2']);
        $o3 = trim($_POST['opt3']);
        $o4 = trim($_POST['opt4']);
        $k  = (int)$_POST['answer_key'];

        $stmt = $conn->prepare("UPDATE quizzes SET question=?, opt1=?, opt2=?, opt3=?, opt4=?, answer_key=? WHERE id=?");
        $stmt->bind_param("ssssssi",$q,$o1,$o2,$o3,$o4,$k,$qid);
        $stmt->execute();

        $msg = "Quiz Updated!";
    }

    if ($_POST['action'] === 'delete_quiz') {

        $qid = (int)$_POST['quiz_id'];
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE id=?");
        $stmt->bind_param("i",$qid);
        $stmt->execute();

        $msg = "Quiz Deleted!";
    }
}

$course = $conn->query("SELECT * FROM courses WHERE id=$course_id")->fetch_assoc();
$quizzes = $conn->query("SELECT * FROM quizzes WHERE course_id=$course_id ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Course</title>
<link rel="stylesheet" href="styles.css">

<style>
    .back-btn{
    position:fixed;
    top:25px;
    right:35px;
    padding:10px 18px;
    background:#111827;
    color:#fff;
    text-decoration:none;
    border-radius:8px;
    font-size:16px;
    font-weight:600;
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
    transition:0.3s ease;
    z-index:999;
}

.back-btn:hover{
    background:#2563eb;
    transform:translateY(-3px);
}

body{
    font-family: Arial, sans-serif;
    font-size:19px;
    min-height:100vh;

    /* Multi color animated gradient */
    background: linear-gradient(-45deg, #667eea, #764ba2, #ff6a88, #ffcc70);
    background-size:400% 400%;
    animation: gradientMove 12s ease infinite;
    transition: all 0.6s ease;
}

/* Scroll change effect */
body.scrolled{
    background: linear-gradient(-45deg, #43cea2, #185a9d, #ff9a9e, #fad0c4);
    background-size:400% 400%;
}

/* Smooth gradient animation */
@keyframes gradientMove{
    0%{ background-position:0% 50%; }
    25%{ background-position:50% 100%; }
    50%{ background-position:100% 50%; }
    75%{ background-position:50% 0%; }
    100%{ background-position:0% 50%; }
}

.section {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(8px);
    padding:35px;
    border-radius:14px;
    margin:35px auto;
    width:88%;
    box-shadow:0 15px 40px rgba(0,0,0,0.08);
}

input, textarea {
    width:100%;
    padding:14px;
    margin-bottom:14px;
    font-size:17px;
    border-radius:8px;
    border:1px solid #ddd;
}

button {
    padding:12px 18px;
    font-size:17px;
    background:#10b981;
    color:#fff;
    border:0;
    border-radius:8px;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 18px rgba(0,0,0,0.15);
}

.quiz-box {
    border:1px solid #eee;
    padding:25px;
    margin-bottom:25px;
    border-radius:12px;
    background:#ffffff;
}
</style>


<script>
window.addEventListener("scroll",function(){
    if(window.scrollY>50){
        document.body.classList.add("scrolled");
    }else{
        document.body.classList.remove("scrolled");
    }
});
</script>

</head>
<body>
<a href="admin_dashboard.php" class="back-btn">← Back</a>

<h2 style="margin-left:7%;">Edit Course</h2>

<?php if($msg): ?>
<p style="color:green;margin-left:7%;"><?php echo $msg; ?></p>
<?php endif; ?>

<div class="section">
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="update_course">

Title:
<input name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required>

Short Description:
<input name="short_desc" value="<?php echo htmlspecialchars($course['short_desc']); ?>">

Content:
<textarea name="content"><?php echo htmlspecialchars($course['content']); ?></textarea>

Current Image:<br>
<?php if($course['image']): ?>
<img src="<?php echo $course['image']; ?>" width="200"><br>
<?php endif; ?>
Change Image:
<input type="file" name="image">

<br>

Current Video:<br>
<?php if($course['video']): ?>
<a href="<?php echo $course['video']; ?>" target="_blank">View Video</a><br>
<?php endif; ?>
Change Video:
<input type="file" name="video">

<br>

Current Document:<br>
<?php if($course['document']): ?>
<a href="<?php echo $course['document']; ?>" target="_blank">View Document</a><br>
<?php endif; ?>
Change Document:
<input type="file" name="document">

<br>
<button>Update Course</button>
</form>
</div>

<h3 style="margin-left:7%;">Edit Quizzes</h3>

<div class="section">
<?php while($q = $quizzes->fetch_assoc()): ?>
<div class="quiz-box">
<form method="post">
<input type="hidden" name="quiz_id" value="<?php echo $q['id']; ?>">

Question:
<textarea name="question"><?php echo htmlspecialchars($q['question']); ?></textarea>

Option 1:
<input name="opt1" value="<?php echo htmlspecialchars($q['opt1']); ?>">

Option 2:
<input name="opt2" value="<?php echo htmlspecialchars($q['opt2']); ?>">

Option 3:
<input name="opt3" value="<?php echo htmlspecialchars($q['opt3']); ?>">

Option 4:
<input name="opt4" value="<?php echo htmlspecialchars($q['opt4']); ?>">

Answer Key:
<input name="answer_key" value="<?php echo $q['answer_key']; ?>">

<button type="submit" name="action" value="update_quiz">Update Quiz</button>

<button type="submit"
        name="action"
        value="delete_quiz"
        style="background:#ef4444;margin-left:8px;"
        onclick="return confirm('Delete this quiz?')">
        Delete
</button>

</form>
</div>
<?php endwhile; ?>
</div>

</body>
</html>
