<?php
session_start();
session_unset();
session_destroy();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Logout Success</title>

  <style>
    body {
      background:#f3f4f6;
      display:flex;
      align-items:center;
      justify-content:center;
      height:100vh;
      margin:0;
      font-family:Arial, sans-serif;
    }
    .popup-box {
      width:360px;
      background:#fff;
      border-radius:10px;
      box-shadow:0 8px 25px rgba(0,0,0,0.15);
      text-align:center;
      padding:25px 30px;
      animation:fadeIn 0.3s ease;
    }
    .popup-box h2 {
      margin:10px 0 5px;
      font-size:22px;
      color:#2d6a4f;
    }
    .popup-box p {
      font-size:14px;
      color:#555;
      margin-bottom:20px;
    }
    .popup-box button {
      background:#7b3fe4;
      color:#fff;
      border:0;
      padding:10px 20px;
      border-radius:6px;
      font-size:14px;
      cursor:pointer;
      font-weight:bold;
    }
    .popup-box button:hover {
      opacity:0.9;
    }
    .check-icon {
      font-size:60px;
      color:#38b000;
    }
    @keyframes fadeIn {
      from {opacity:0; transform:scale(0.9);}
      to {opacity:1; transform:scale(1);}
    }
  </style>

</head>
<body>

  <div class="popup-box">
      <div class="check-icon">✔</div>
      <h2>Success!</h2>
      <p>Logged out successfully.</p>
      <button onclick="window.location.href='admin_login.php'">OK</button>
  </div>

</body>
</html>
