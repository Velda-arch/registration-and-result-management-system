<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Our Education — Quality Education</title>


  <style>
    :root{--navy:#1e3a5f;--green:#2d6a4f;--gold:#c9a84c;--light:#f0f4f8;}
    *,*::before,*::after{box-sizing:border-box;}
    body{font-family:'DM Sans',sans-serif;margin:0;background:var(--light);}
    .topnav{background:var(--navy);padding:13px 0;border-bottom:3px solid var(--gold);position:sticky;top:0;z-index:100;}
    .brand{display:flex;align-items:center;gap:12px;text-decoration:none;}
    .brand-logo{width:42px;height:42px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:19px;color:var(--navy);font-weight:700;}
    .brand h1{color:#fff;font-family:'Playfair Display',serif;font-size:1.1rem;margin:0;}
    .brand small{color:rgba(255,255,255,.5);font-size:.66rem;}
    .btn-apply{background:var(--gold);color:var(--navy);border:none;border-radius:8px;padding:9px 22px;font-size:.88rem;font-weight:700;text-decoration:none;transition:opacity .2s;}
    .btn-apply:hover{opacity:.88;color:var(--navy);}

    .hero{background:linear-gradient(135deg,var(--navy) 0%,var(--green) 100%);padding:90px 0 80px;position:relative;overflow:hidden;}
    .hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fff' fill-opacity='0.03'%3E%3Cpath d='M50 50c0-5.5 4.5-10 10-10s10 4.5 10 10-4.5 10-10 10c0 5.5-4.5 10-10 10s-10-4.5-10-10 4.5-10 10-10zM10 10c0-5.5 4.5-10 10-10s10 4.5 10 10-4.5 10-10 10c0 5.5-4.5 10-10 10S0 25.5 0 20s4.5-10 10-10z'/%3E%3C/g%3E%3C/svg%3E");}
    .hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(201,168,76,.15);border:1px solid rgba(201,168,76,.4);color:var(--gold);border-radius:50px;padding:6px 18px;font-size:.76rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:20px;}
    .hero h1{font-family:'Playfair Display',serif;color:#fff;font-size:2.8rem;font-weight:800;line-height:1.15;margin-bottom:18px;}
    .hero h1 span{color:var(--gold);}
    .hero p{color:rgba(255,255,255,.7);font-size:.97rem;line-height:1.75;margin-bottom:32px;max-width:480px;}
    .btn-hero{background:var(--gold);color:var(--navy);border:none;border-radius:10px;padding:13px 32px;font-size:.97rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:opacity .2s,transform .1s;}
    .btn-hero:hover{opacity:.9;transform:translateY(-1px);color:var(--navy);}

    .stats-bar{background:#fff;padding:24px 0;border-bottom:1px solid #e5e7eb;box-shadow:0 4px 14px rgba(0,0,0,.05);}
    .stat-item{text-align:center;}
    .stat-num{font-size:1.8rem;font-weight:800;color:var(--navy);font-family:'Playfair Display',serif;}
    .stat-lbl{font-size:.74rem;color:#6b7280;text-transform:uppercase;letter-spacing:1px;}

    .section-title{font-family:'Playfair Display',serif;font-size:1.85rem;color:var(--navy);text-align:center;margin-bottom:8px;}
    .section-sub{text-align:center;color:#6b7280;font-size:.92rem;margin-bottom:42px;}

    .feat-card{background:#fff;border-radius:14px;padding:28px 24px;border:1px solid #e5e7eb;height:100%;transition:transform .2s,box-shadow .2s;}
    .feat-card:hover{transform:translateY(-3px);box-shadow:0 10px 28px rgba(30,58,95,.1);}
    .feat-icon{width:52px;height:52px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-bottom:16px;}
    .feat-card h5{font-family:'Playfair Display',serif;color:var(--navy);font-size:.97rem;margin-bottom:8px;}
    .feat-card p{color:#6b7280;font-size:.85rem;line-height:1.65;margin:0;}

    .cta-banner{background:linear-gradient(135deg,var(--navy),var(--green));padding:65px 0;text-align:center;}
    .cta-banner h2{font-family:'Playfair Display',serif;color:#fff;font-size:2rem;margin-bottom:12px;}
    .cta-banner p{color:rgba(255,255,255,.7);margin-bottom:28px;font-size:.95rem;}

    footer{background:var(--navy);color:rgba(255,255,255,.4);text-align:center;padding:18px;font-size:.78rem;}

    @media(max-width:576px){
      .hero h1{font-size:1.9rem;}
      .hero{padding:60px 0 50px;}
    }
  </style>
  <link rel="stylesheet" href="assets/css/bootstrap.css"/>
  <link rel="stylesheet" href="assets/css/icons.css"/>
  <script src="assets/js/bootstrap.bundle.js" defer></script>
</head>
<body>

<nav class="topnav">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="index.php" class="brand">
      <div class="brand-logo">O</div>
      <div><h1>Our Education</h1><small>Quality Education</small></div>
    </a>
    <a href="register.php" class="btn-apply"><i class="bi bi-pencil-square me-1"></i>Apply Now</a>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <div class="hero-badge"><i class="bi bi-mortarboard-fill"></i> Admissions Open 2025/2026</div>
    <h1>Build Your Future at<br><span>Our Education</span></h1>
    <p>Join a community where learning matters. We offer quality education across multiple departments, taught by experienced teachers.</p>

  </div>
</section>

<div class="stats-bar">
  <div class="container">
    <div class="row text-center g-3">
      <div class="col-6 col-md-3"><div class="stat-num">5+</div><div class="stat-lbl">Departments</div></div>
      <div class="col-6 col-md-3"><div class="stat-num">500+</div><div class="stat-lbl">Graduates</div></div>
      <div class="col-6 col-md-3"><div class="stat-num">20+</div><div class="stat-lbl">Years of Excellence</div></div>
      <div class="col-6 col-md-3"><div class="stat-num">98%</div><div class="stat-lbl">Pass Rate</div></div>
    </div>
  </div>
</div>

<section class="py-5 mt-2">
  <div class="container py-3">
    <h2 class="section-title">How to Get In</h2>
    <p class="section-sub">Our admission process is simple and fully online.</p>
    <div class="row g-3">
      <div class="col-md-3"><div class="feat-card text-center">
        <div class="feat-icon mx-auto" style="background:#eff6ff;"><i class="bi bi-pencil-square" style="color:#2563eb;"></i></div>
        <h5>Fill the Form</h5>
        <p>Enter your name, email, age and choose the department you want to join.</p>
      </div></div>
      <div class="col-md-3"><div class="feat-card text-center">
        <div class="feat-icon mx-auto" style="background:#fef9c3;"><i class="bi bi-upload" style="color:#ca8a04;"></i></div>
        <h5>Upload Papers</h5>
        <p>Upload your A-Level result slip and birth certificate. ID is optional.</p>
      </div></div>
      <div class="col-md-3"><div class="feat-card text-center">
        <div class="feat-icon mx-auto" style="background:#dcfce7;"><i class="bi bi-shield-check" style="color:#16a34a;"></i></div>
        <h5>We Review</h5>
        <p>Our team checks your documents and places you in the right class.</p>
      </div></div>
      <div class="col-md-3"><div class="feat-card text-center">
        <div class="feat-icon mx-auto" style="background:#f3e8ff;"><i class="bi bi-envelope-check" style="color:#9333ea;"></i></div>
        <h5>Get Your Results</h5>
        <p>After your exams, your results are sent straight to your email address.</p>
      </div></div>
    </div>
  </div>
</section>

<section class="cta-banner">
  <div class="container">
    <h2>Ready to Join Us?</h2>
    <p>Applications for 2025/2026 are now open. Don't wait — apply today.</p>
    <a href="register.php" class="btn-hero"><i class="bi bi-pencil-square me-2"></i>Apply for Admission</a>
  </div>
</section>

<footer>© <?=date('Y')?> Our Education · All rights reserved</footer>
</body>
</html>
