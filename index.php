<?php
// updated index - improved colors & simple design (hero uses gradient color, no image)
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Bright Future - E-Learning</title>
  <link rel="stylesheet" href="styles.css">
  <!-- small page-specific style overrides -->
  <style>
    :root{
      --primary: #7b3fe4;
      --accent: #6f2bd8;
      --muted: #6b7280;
      --card-bg: #ffffff;
      --page-bg: #fbfbff;
      --pink-1: #ff7ab6;
      --pink-2: #9b4cff;
      --white-trans: rgba(255,255,255,0.12);
    }
    *{box-sizing:border-box}
    body{background:var(--page-bg);margin:0;font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;}
    .site-header{
      background:linear-gradient(90deg,#fff 0%, rgba(123,63,228,0.04) 100%);
      padding:18px 24px;
      box-shadow:0 4px 18px rgba(11,15,30,0.04);
      display:flex;
      align-items:center;
      justify-content:space-between;
    }
    .site-header .brand{color:var(--primary); font-weight:800; font-size:20px; letter-spacing:1px;}
    .site-header nav a{color:#333; margin-left:16px; text-decoration:none; padding:8px 10px; border-radius:8px; font-weight:600}
    .site-header nav a:hover{background:rgba(123,63,228,0.08); color:var(--accent)}

    .container{max-width:1100px;margin:20px auto;padding:0 16px}

    /* HERO: color-only gradient (no image) */
    .hero{
      position:relative;
      border-radius:14px;
      overflow:hidden;
      margin-top:18px;
      display:flex;
      align-items:center;
      gap:24px;
      padding:36px;
      background: linear-gradient(120deg, var(--pink-1), var(--pink-2));
      color: #fff;
      box-shadow: 0 18px 46px rgba(155,76,255,0.12);
      min-height:240px;
    }

    /* subtle decorative shapes */
    .hero::before, .hero::after{
      content: "";
      position:absolute;
      border-radius:50%;
      opacity:0.12;
      pointer-events:none;
    }
    .hero::before{
      width:260px;height:260px; right:-70px; top:-60px;
      background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.20), transparent 30%);
    }
    .hero::after{
      width:160px;height:160px; left:-40px; bottom:-40px;
      background: radial-gradient(circle at 40% 40%, rgba(255,255,255,0.12), transparent 30%);
    }

    .hero-content{flex:1;padding:0 10px; z-index:1}
    .hero-content h1{font-size:38px;margin:0 0 10px;line-height:1.05;font-weight:800;text-shadow:0 8px 28px rgba(0,0,0,0.12);}
    .hero-content p{color:rgba(255,255,255,0.95);margin:0 0 18px;font-size:15px}

    .btn{display:inline-block;background:linear-gradient(90deg,#fff9,#ffffff); color:#fff;padding:10px 18px;border-radius:28px;text-decoration:none;
         background: linear-gradient(90deg,#ffffff, rgba(255,255,255,0.9));
         color:var(--pink-2);
         font-weight:700; box-shadow: 0 8px 24px rgba(0,0,0,0.08); border:none; }

    /* small translucent callout on right */
    .hero-side{width:320px;max-width:38%; z-index:1}
    .hero-side .callout{
      background: rgba(255,255,255,0.08);
      padding:16px;
      border-radius:10px;
      color:#fff;
      border: 1px solid rgba(255,255,255,0.06);
    }
    .hero-side .callout h4{margin:0 0 8px;font-size:16px}
    .hero-side .callout p{margin:0 0 12px;color:rgba(255,255,255,0.95);font-size:14px}
    .hero-side .callout .small-btn{display:inline-block;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,0.12);color:#fff;text-decoration:none;font-weight:700}

    .features{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-top:26px}
    .card{background:var(--card-bg);padding:18px;border-radius:10px;box-shadow:0 6px 18px rgba(16,24,40,0.04);border:1px solid rgba(10,10,10,0.03)}
    .card h3{margin:0 0 8px;color:#6b2bd8}
    .card p{color:var(--muted);margin:0}

    .site-footer{margin-top:34px;text-align:center;color:var(--muted);padding:18px 0}

    @media (max-width:900px){
      .hero{flex-direction:column;padding:22px;min-height:auto}
      .hero-side{width:100%;max-width:100%;margin-top:12px}
      .site-header nav{display:none}
      .hero-content h1{font-size:28px}
    }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="brand">BRIGHT FUTURE</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="courses.php">Courses</a>
      <a href="student_login.php">Student Login</a>
      <a href="admin_login.php">Admin Login</a>
    </nav>
  </header>

  <main class="container" style="max-width:1100px;margin:20px auto;padding:0 16px">
    <section class="hero" aria-label="hero section">
      <div class="hero-content">
        <h1>Learn Smart, Lead Smart</h1>
        <p>Interactive courses, short videos and quizzes — build your computer science skills step by step.</p>
        <a class="btn" href="courses.php">Get Started</a>
      </div>

   
      </div>
    </section>

    <section class="features" aria-label="features">
      <div class="card">
        <h3>Courses</h3>
        <p>Hands-on courses with video lessons and materials.</p>
      </div>
      <div class="card">
        <h3>Quizzes</h3>
        <p>Assess learning with quizzes after each lesson — scores stored for review.</p>
      </div>
      <div class="card">
        <h3>Admin Panel</h3>
        <p>Admins can upload videos, add courses and create quizzes for students.</p>
      </div>
    </section>
  </main>
  <!-- Visible hero-side box (REMOVE aria-hidden attribute) -->
<div class="hero-side">
  <div class="callout" role="note" aria-label="Quick cources">
    <h4>Quick cources</h4>
    <p>Short focused lessons and downloadable resources. Login to join and track progress.</p>
    <a class="small-btn" href="student_login.php?login_required=1&next=courses.php">Join</a>
  </div>
</div>


  <footer class="site-footer">
    <p>© 2025 Bright Future</p>
  </footer>
</body>
</html>
