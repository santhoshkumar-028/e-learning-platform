<?php
session_start(); require 'db_connect.php';
$msg=''; $mode=$_POST['mode']??'login';

if($_SERVER['REQUEST_METHOD']=='POST'){
 if($mode=='register'){
  if($_POST['password']!=$_POST['cpassword']) $msg="Passwords not match";
  else{
   $h=password_hash($_POST['password'],PASSWORD_DEFAULT);
   $q=$conn->prepare("INSERT INTO admins(username,password) VALUES(?,?)");
   if($q && $q->bind_param("ss",$_POST['username'],$h) && $q->execute())
     $msg="Admin registered. Please login.";
   else $msg="Admin already exists";
  }
 }

 if($mode=='login'){
  $q=$conn->prepare("SELECT id,password FROM admins WHERE username=?");
  $q->bind_param("s",$_POST['username']); $q->execute(); $r=$q->get_result();
  if($r->num_rows){
   $u=$r->fetch_assoc();
   if(password_verify($_POST['password'],$u['password'])){
    $_SESSION['admin_id']=$u['id'];
    header("Location:admin_dashboard.php"); exit;
  }}
  $msg="Invalid admin login";
 }
}
?>
<!doctype html><html><head><title>Admin Login</title>
<style>
body{margin:0;font-family:sans-serif;background:linear-gradient(135deg,#7b3fe4,#ec4899)}
.box{max-width:420px;margin:80px auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 10px 30px #0003}
h2{text-align:center;color:#7b3fe4}
.tab{display:flex;gap:6px;margin-bottom:10px}
.tab div{flex:1;padding:8px;text-align:center;font-weight:700;border-radius:6px;cursor:pointer}
.active{background:#7b3fe4;color:#fff}
.msg{background:#fee2e2;color:#b91c1c;padding:8px;border-radius:6px;margin-bottom:8px}
input,button{width:100%;padding:8px;margin-top:8px}
button{background:#7b3fe4;color:#fff;border:0;border-radius:6px;font-weight:700}
.back{position:fixed;top:20px;right:20px;background:#fff;padding:8px 14px;
border-radius:20px;text-decoration:none;color:#7b3fe4;font-weight:700}
</style>
<script>
function show(x){
 login.style.display=x=='l'?'block':'none';
 reg.style.display=x=='r'?'block':'none';
 tl.classList.toggle('active',x=='l');
 tr.classList.toggle('active',x=='r');
}
</script></head><body>

<a href="index.php" class="back">← Back</a>

<div class="box">
<h2>BRIGHT FUTURE<br><small>Admin Panel</small></h2>

<div class="tab">
 <div id="tl" class="active" onclick="show('l')">Login</div>
 <div id="tr" onclick="show('r')">Register</div>
</div>

<?php if($msg):?><div class="msg"><?=$msg?></div><?php endif;?>

<div id="login">
<form method="post">
<input type="hidden" name="mode" value="login">
<input name="username" placeholder="Admin Username" required>
<input type="password" name="password" placeholder="Password" required>
<button>Login</button>
</form></div>

<div id="reg" style="display:none">
<form method="post">
<input type="hidden" name="mode" value="register">
<input name="username" placeholder="Admin Username" required>
<input type="password" name="password" placeholder="Password" required>
<input type="password" name="cpassword" placeholder="Confirm Password" required>
<button>Register</button>
</form></div>
</div>

<script>show('l')</script>
</body></html>
