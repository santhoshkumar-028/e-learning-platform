<?php
session_start(); require 'db_connect.php';
$msg=''; $next=$_GET['next']??'student_dashboard.php';
if(isset($_GET['login_required']))$msg="Please login to attend the course.";

if(($_POST['action']??'')=='login'){
 if($_POST['password']!=$_POST['cpassword'])$msg="Passwords do not match";
 else{
  $q=$conn->prepare("SELECT id,password FROM students WHERE email=?");
  $q->bind_param("s",$_POST['email']); $q->execute(); $r=$q->get_result();
  if($r->num_rows && password_verify($_POST['password'],$r->fetch_assoc()['password'])){
    $_SESSION['student_id']=1; header("Location:$next"); exit;
  } $msg="Invalid login details";
 }}
if(($_POST['action']??'')=='register'){
 if($_POST['password']!=$_POST['cpassword'])$msg="Passwords do not match";
 else{
  $h=password_hash($_POST['password'],PASSWORD_DEFAULT);
  $q=$conn->prepare("INSERT INTO students(name,email,password)VALUES(?,?,?)");
  if($q && $q->bind_param("sss",$_POST['name'],$_POST['email'],$h) && $q->execute())
    $msg="Registration successful. Please login.";
  else $msg="Email already exists";
 }}
?>
<!doctype html><html><head><title>Bright Future</title>
<style>
body{margin:0;font-family:sans-serif;background:linear-gradient(135deg,#7b3fe4,#ec4899)}
.box{max-width:420px;margin:70px auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 10px 30px #0003}
h2{text-align:center;color:#7b3fe4}
.tab{display:flex;gap:8px;margin-bottom:10px}
.tab div{flex:1;padding:8px;text-align:center;border-radius:6px;font-weight:700;cursor:pointer}
.active{background:#7b3fe4;color:#fff}
.msg{background:#eef2ff;color:#4338ca;padding:8px;border-radius:6px;margin-bottom:8px}
input,button{width:100%;padding:8px;margin-top:8px}
button{background:#7b3fe4;color:#fff;border:0;border-radius:6px;font-weight:700}
.back{position:fixed;top:20px;right:20px;background:#fff;padding:8px 14px;
border-radius:20px;text-decoration:none;color:#7b3fe4;font-weight:700}
</style>
<script>
function s(x){login.style.display=x=='l'?'block':'none';
reg.style.display=x=='r'?'block':'none';
tl.classList.toggle('active',x=='l');
tr.classList.toggle('active',x=='r');}
</script></head><body>

<a href="index.php" class="back">← Back</a>

<div class="box">
<h2>BRIGHT FUTURE<br><small>Student Login</small></h2>
<div class="tab">
 <div id="tl" class="active" onclick="s('l')">Login</div>
 <div id="tr" onclick="s('r')">Register</div>
</div>
<?php if($msg):?><div class="msg"><?=$msg?></div><?php endif;?>

<div id="login">
<form method="post">
<input type="hidden" name="action" value="login">
<input name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<input type="password" name="cpassword" placeholder="Confirm Password" required>
<input type="hidden" name="next" value="<?=$next?>">
<button>Login</button></form></div>

<div id="reg" style="display:none">
<form method="post">
<input type="hidden" name="action" value="register">
<input name="name" placeholder="Full Name" required>
<input name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<input type="password" name="cpassword" placeholder="Confirm Password" required>
<button>Register</button></form></div>
</div>

<script>s('l')</script>
</body></html>
