<?php
require_once __DIR__ . '/includes/config.php';

// Redirect students away silently if no secret key
if (($_GET['ref'] ?? '') !== LOGIN_KEY) {
    header('Location: register.php');
    exit;
}

// Already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'lecturer/dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) {
        $error = 'Please fill in both fields.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT id,full_name,password,role FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $u = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($u && password_verify($password, $u['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $u['id'];
            $_SESSION['full_name'] = $u['full_name'];
            $_SESSION['role']      = $u['role'];
            header('Location: ' . ($u['role'] === 'admin' ? 'admin/dashboard.php' : 'lecturer/dashboard.php'));
            exit;
        }
        $error = 'Wrong email or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Sign In — Our Education</title>


  <style>
    :root{--navy:#1e3a5f;--green:#2d6a4f;--gold:#c9a84c;}
    *,*::before,*::after{box-sizing:border-box;}
    body{font-family:'DM Sans',sans-serif;min-height:100vh;margin:0;display:flex;background:#0f2034;}

    .left-panel{flex:1;background:linear-gradient(160deg,#1e3a5f 0%,#2d6a4f 60%,#1a4535 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 40px;position:relative;overflow:hidden;}
    .left-panel::before{content:'';position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;background:rgba(201,168,76,.12);}
    .left-panel::after{content:'';position:absolute;bottom:-60px;left:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.06);}
    .l-emblem{width:84px;height:84px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:38px;color:var(--navy);font-weight:700;margin-bottom:24px;box-shadow:0 8px 28px rgba(201,168,76,.35);position:relative;z-index:1;}
    .left-panel h1{font-family:'Playfair Display',serif;color:#fff;font-size:1.9rem;text-align:center;margin-bottom:10px;line-height:1.2;position:relative;z-index:1;}
    .left-panel p{color:rgba(255,255,255,.6);text-align:center;font-size:.9rem;line-height:1.7;max-width:280px;position:relative;z-index:1;}
    .tagline{margin-top:28px;border-top:1px solid rgba(255,255,255,.15);padding-top:20px;color:var(--gold);font-size:.75rem;letter-spacing:2px;text-transform:uppercase;text-align:center;position:relative;z-index:1;}

    .right-panel{width:440px;flex-shrink:0;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:52px 44px;}
    .r-head{text-align:center;margin-bottom:32px;width:100%;}
    .r-head h2{font-family:'Playfair Display',serif;color:var(--navy);font-size:1.7rem;margin-bottom:5px;}
    .r-head p{color:#6b7280;font-size:.86rem;margin:0;}
    .form-label{font-size:.82rem;font-weight:600;color:#374151;margin-bottom:5px;}
    .input-group-text{background:#f9fafb;border-right:none;border-color:#d1d5db;color:#6b7280;}
    .form-control{border-left:none;border-color:#d1d5db;padding:10px 13px;font-size:.9rem;}
    .form-control:focus{box-shadow:none;border-color:var(--navy);}
    .input-group:focus-within .input-group-text{border-color:var(--navy);}
    .pw-toggle{background:#f9fafb;border-left:none;border-color:#d1d5db;cursor:pointer;color:#6b7280;}
    .pw-toggle:hover{color:var(--navy);}
    .btn-signin{width:100%;background:linear-gradient(135deg,var(--navy),var(--green));color:#fff;border:none;border-radius:9px;padding:12px;font-size:.97rem;font-weight:600;cursor:pointer;transition:opacity .2s,transform .1s;margin-top:6px;}
    .btn-signin:hover{opacity:.9;transform:translateY(-1px);}
    .err-box{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:11px 15px;font-size:.84rem;display:flex;align-items:center;gap:9px;margin-bottom:18px;width:100%;}
    .bottom-note{text-align:center;margin-top:28px;padding-top:20px;border-top:1px solid #f3f4f6;font-size:.81rem;color:#9ca3af;width:100%;}
    .bottom-note a{color:var(--navy);font-weight:600;text-decoration:none;}

    @media(max-width:768px){
      body{flex-direction:column;}
      .left-panel{padding:36px 24px;min-height:200px;flex:none;}
      .left-panel h1{font-size:1.5rem;}
      .right-panel{width:100%;padding:36px 24px;}
    }
  </style>
  <link rel="stylesheet" href="assets/css/bootstrap.css"/>
  <link rel="stylesheet" href="assets/css/icons.css"/>
  <script src="assets/js/bootstrap.bundle.js" defer></script>
</head>
<body>

<div class="left-panel">
  <div class="l-emblem">O</div>
  <h1>Our Education</h1>
  <p>Building bright futures through quality education and dedicated teaching.</p>
  <div class="tagline">Est. 2005 · Quality Education</div>
</div>

<div class="right-panel">
  <div class="r-head">
    <h2>Welcome Back</h2>
    <p>Sign in to go to your dashboard</p>
  </div>

  <?php if ($error): ?>
  <div class="err-box">
    <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;"></i>
    <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <form method="POST" id="loginForm" style="width:100%;">
    <div class="mb-3">
      <label class="form-label">Email Address</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input type="email" name="email" class="form-control" placeholder="Enter your email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete="email"/>
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label">Password</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input type="password" name="password" id="pw" class="form-control" placeholder="Enter your password" required autocomplete="current-password"/>
        <button type="button" class="btn pw-toggle border" onclick="togglePw()"><i class="bi bi-eye" id="pwEye"></i></button>
      </div>
    </div>
    <button type="submit" class="btn-signin" id="signBtn">
      <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
    </button>
    <input type="hidden" name="ref" value="<?= LOGIN_KEY ?>"/>
  </form>

  <!--<div class="bottom-note">
    New student? <a href="register.php">Apply for admission here</a>
  </div>
  -->
</div>
<script>
function togglePw(){
  const p=document.getElementById('pw'),i=document.getElementById('pwEye');
  p.type=p.type==='password'?'text':'password';
  i.className=p.type==='password'?'bi bi-eye':'bi bi-eye-slash';
}
document.getElementById('loginForm').addEventListener('submit',function(){
  const b=document.getElementById('signBtn');
  b.disabled=true;b.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Signing in…';
});
</script>
</body>
</html>
