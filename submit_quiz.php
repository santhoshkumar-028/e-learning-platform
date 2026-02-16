<?php
require 'db_connect.php'; session_start();
if($_SERVER['REQUEST_METHOD']!='POST'||!isset($_POST['course_id'])){
 header("Location:courses.php"); exit;
}

$cid=(int)$_POST['course_id'];
$ans=$_POST['ans']??[]; $score=0; $total=0;

if($ans){
 $ids=array_filter(array_map('intval',array_keys($ans)));
 if($ids){
  $total=count($ids);
  $r=$conn->query("SELECT id,answer_key FROM quizzes WHERE id IN(".implode(',',$ids).")");
  while($q=$r->fetch_assoc())
   if(($ans[$q['id']]??0)==$q['answer_key']) $score++;
 }
}

$saved=0;
if(isset($_SESSION['student_id'])){
 $q=$conn->prepare("INSERT INTO results(student_id,course_id,score) VALUES(?,?,?)");
 $q->bind_param("iii",$_SESSION['student_id'],$cid,$score);
 $saved=$q->execute()?1:0;
}
?>
<!doctype html><html><head>
<title>Quiz Result</title>
<style>
body{margin:0;font-family:sans-serif;background:#f5f6fa}
.modal{position:fixed;inset:0;background:#0007;
display:flex;align-items:center;justify-content:center}
.box{background:#fff;width:420px;max-width:95%;
border-radius:12px;padding:28px;text-align:center}
.tick{font-size:52px;color:#22c55e}
h2{margin:10px 0;color:#7b3fe4}
.msg{color:#555;margin-bottom:12px}
.score{background:#ede9fe;color:#5b2bd8;
display:inline-block;padding:10px 16px;border-radius:20px;
font-weight:700;margin:10px 0}
.btn{margin:8px;padding:10px 16px;border:0;border-radius:8px;
font-weight:700;cursor:pointer}
.primary{background:#7b3fe4;color:#fff}
.gray{background:#eee}
</style></head><body>

<div class="modal">
 <div class="box">
  <div class="tick">✔</div>
  <h2>Congratulations 🎉</h2>
  <div class="msg">You have finished this course successfully!</div>

  <div class="score">Score : <?=$score?> / <?=$total?:'--'?></div>

  <div class="msg">
   <?=$saved?'Your score is saved.':'Login to save your score.'?>
  </div>

  <button class="btn primary" onclick="location.href='courses.php'">OK</button>
  <button class="btn gray" onclick="location.href='course.php?id=<?=$cid?>'">View Course</button>
 </div>
</div>

</body></html>
