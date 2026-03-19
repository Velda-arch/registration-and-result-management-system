<?php
require_once __DIR__ . '/includes/config.php';
$success = $error = '';
$depts = getDB()->query("SELECT id, name FROM departments ORDER BY name");
$deptList = [];
while ($d = $depts->fetch_assoc()) $deptList[] = $d;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = sanitize($_POST['full_name'] ?? '');
    $email  = sanitize($_POST['email'] ?? '');
    $age    = (int)($_POST['age'] ?? 0);
    $deptId = (int)($_POST['department_id'] ?? 0);
    $errors = [];

    if (strlen($name) < 3)  $errors[] = 'Please enter your full name (at least 3 letters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if ($age < 16 || $age > 60) $errors[] = 'Your age must be between 16 and 60.';
    if (!$deptId) $errors[] = 'Please choose your department.';

    $db = getDB();
    $ck = $db->prepare("SELECT id FROM students WHERE email=?");
    $ck->bind_param("s", $email); $ck->execute(); $ck->store_result();
    if ($ck->num_rows > 0) $errors[] = 'This email is already registered.';
    $ck->close();

    $allowed = ['image/jpeg','image/png','image/webp','application/pdf'];
    $files   = ['alevel_slip'=>null,'birth_certificate'=>null,'id_document'=>null];
    $fields  = [
        'alevel_slip'       => ['required'=>true,  'label'=>'A-Level Result Slip'],
        'birth_certificate' => ['required'=>true,  'label'=>'Birth Certificate'],
        'id_document'       => ['required'=>false, 'label'=>'ID / Passport'],
    ];

    foreach ($fields as $field => $cfg) {
        if (!empty($_FILES[$field]['name'])) {
            $f = $_FILES[$field];
            if ($f['error'] !== UPLOAD_ERR_OK) { $errors[] = "Could not upload {$cfg['label']}."; continue; }
            if ($f['size'] > MAX_FILE_SIZE)     { $errors[] = "{$cfg['label']} must be under 5MB."; continue; }
            if (!in_array($f['type'], $allowed)){ $errors[] = "{$cfg['label']} must be a photo (JPG/PNG) or PDF."; continue; }
            $ext  = pathinfo($f['name'], PATHINFO_EXTENSION);
            $name2 = $field . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dir  = UPLOAD_PATH . $field . '/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            if (move_uploaded_file($f['tmp_name'], $dir . $name2)) {
                $files[$field] = $field . '/' . $name2;
            } else {
                $errors[] = "Could not save {$cfg['label']}.";
            }
        } elseif ($cfg['required']) {
            $errors[] = "{$cfg['label']} is required — please upload it.";
        }
    }

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO students (full_name,email,age,department_id,alevel_slip,birth_certificate,id_document) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("ssiisss", $name, $email, $age, $deptId, $files['alevel_slip'], $files['birth_certificate'], $files['id_document']);
        if ($stmt->execute()) {
            $success = "Your application has been sent! Our team will review your documents and get back to you soon.";
            $name = $email = ''; $age = 0; $deptId = 0;
        } else {
            $error = 'Something went wrong. Please try again.';
        }
        $stmt->close();
    } else {
        $error = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Apply for Admission — Our Education</title>


  <style>
    :root{--navy:#1e3a5f;--green:#2d6a4f;--gold:#c9a84c;--light:#f0f4f8;}
    *,*::before,*::after{box-sizing:border-box;}
    body{font-family:'DM Sans',sans-serif;background:var(--light);margin:0;}
    .topnav{background:var(--navy);padding:13px 0;border-bottom:3px solid var(--gold);}
    .brand{display:flex;align-items:center;gap:12px;text-decoration:none;}
    .brand-logo{width:42px;height:42px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:19px;color:var(--navy);font-weight:700;}
    .brand h1{color:#fff;font-family:'Playfair Display',serif;font-size:1.1rem;margin:0;}
    .brand small{color:rgba(255,255,255,.5);font-size:.66rem;}
    .hero{background:linear-gradient(135deg,var(--navy) 0%,var(--green) 100%);padding:46px 0 40px;text-align:center;position:relative;overflow:hidden;}
    .hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23fff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4z'/%3E%3C/g%3E%3C/svg%3E");}
    .hero h2{font-family:'Playfair Display',serif;color:#fff;font-size:1.9rem;margin-bottom:7px;position:relative;}
    .hero p{color:rgba(255,255,255,.72);font-size:.92rem;margin:0;position:relative;}
    .steps{display:flex;justify-content:center;gap:8px;margin-top:22px;flex-wrap:wrap;position:relative;}
    .step{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.8);border-radius:50px;padding:5px 16px;font-size:.74rem;letter-spacing:.6px;text-transform:uppercase;}
    .step.on{background:var(--gold);border-color:var(--gold);color:var(--navy);font-weight:700;}
    .form-card{background:#fff;border-radius:14px;box-shadow:0 6px 32px rgba(30,58,95,.10);padding:36px;margin-top:-28px;position:relative;z-index:2;}
    .sec-title{font-family:'Playfair Display',serif;font-size:.95rem;color:var(--navy);border-left:4px solid var(--gold);padding-left:11px;margin-bottom:18px;}
    .form-label{font-size:.82rem;font-weight:600;color:#374151;margin-bottom:5px;}
    .req{color:#dc3545;margin-left:2px;}
    .form-control,.form-select{border:1.5px solid #d1d5db;border-radius:8px;padding:9px 13px;font-size:.88rem;transition:border-color .2s,box-shadow .2s;}
    .form-control:focus,.form-select:focus{border-color:var(--navy);box-shadow:0 0 0 3px rgba(30,58,95,.1);}
    .upload-zone{border:2px dashed #d1d5db;border-radius:10px;padding:20px 14px;text-align:center;cursor:pointer;transition:all .2s;background:var(--light);position:relative;min-height:100px;display:flex;flex-direction:column;align-items:center;justify-content:center;}
    .upload-zone:hover,.upload-zone.on{border-color:var(--navy);background:#e8edf3;}
    .upload-zone input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
    .upload-zone i{font-size:1.8rem;color:var(--navy);margin-bottom:5px;}
    .upload-zone p{margin:0;font-size:.78rem;color:#6b7280;line-height:1.5;}
    .upload-zone .fname{color:var(--green);font-weight:600;font-size:.8rem;margin-top:5px;}
    .u-badge{display:inline-block;border-radius:50px;padding:2px 10px;font-size:.7rem;font-weight:600;margin-top:3px;}
    .u-req{background:#fee2e2;border:1px solid #fecaca;color:#991b1b;}
    .u-opt{background:#fef9c3;border:1px solid #fde047;color:#713f12;}
    .divider{border:0;border-top:1.5px solid #e5e7eb;margin:24px 0;}
    .btn-apply{background:linear-gradient(135deg,var(--navy),var(--green));color:#fff;border:none;border-radius:10px;padding:12px 30px;font-size:.97rem;font-weight:600;width:100%;cursor:pointer;transition:opacity .2s,transform .1s;}
    .btn-apply:hover{opacity:.9;transform:translateY(-1px);}
    .alert-ok{background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:9px;padding:14px 18px;font-size:.88rem;margin-bottom:18px;}
    .alert-err{background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:9px;padding:14px 18px;font-size:.88rem;margin-bottom:18px;}
    .info-tip{background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;padding:11px 15px;font-size:.8rem;color:#1e40af;margin-bottom:20px;}
    footer{background:var(--navy);color:rgba(255,255,255,.4);text-align:center;padding:18px;font-size:.78rem;margin-top:50px;}

    @media(max-width:576px){
      .form-card{padding:22px 16px;}
      .hero h2{font-size:1.4rem;}
    }
  </style>
  <link rel="stylesheet" href="assets/css/bootstrap.css"/>
  <link rel="stylesheet" href="assets/css/icons.css"/>
  <script src="assets/js/bootstrap.bundle.js" defer></script>
</head>
<body>

<nav class="topnav">
  <div class="container">
    <a href="index.php" class="brand">
      <div class="brand-logo">O</div>
      <div><h1>Our Education</h1><small>Quality Education</small></div>
    </a>
  </div>
</nav>

<div class="hero">
  <div class="container">
    <h2>Student Admission Form</h2>
    <p>Fill in your details and upload your documents to apply for admission.</p>
    <div class="steps">
      <span class="step on">1 · Fill Form</span>
      <span class="step">2 · Upload Documents</span>
      <span class="step">3 · Submit</span>
      <span class="step">4 · Wait for Approval</span>
    </div>
  </div>
</div>

<div class="container" style="max-width:780px;">
  <div class="form-card">

    <?php if ($success): ?>
    <div class="alert-ok"><i class="bi bi-check-circle-fill me-2"></i><?= $success ?></div>
    <?php elseif ($error): ?>
    <div class="alert-err"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div>
    <?php endif; ?>

    <div class="info-tip">
      <i class="bi bi-info-circle-fill me-2"></i>
      Fields marked <strong>*</strong> are required. Accepted file types: <strong>JPG, PNG, PDF</strong> (max 5MB each).
      You will be placed in the right class once your application is approved.
    </div>

    <form method="POST" enctype="multipart/form-data" id="regForm" novalidate>

      <h6 class="sec-title"><i class="bi bi-person-fill me-2"></i>Your Details</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Full Name <span class="req">*</span></label>
          <input type="text" name="full_name" class="form-control" placeholder="e.g. Sama Velda"
                 value="<?= htmlspecialchars($name ?? '') ?>" required/>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email Address <span class="req">*</span></label>
          <input type="email" name="email" class="form-control" placeholder="sama.velda@email.com"
                 value="<?= htmlspecialchars($email ?? '') ?>" required/>
        </div>
        <div class="col-md-4">
          <label class="form-label">Age <span class="req">*</span></label>
          <input type="number" name="age" class="form-control" placeholder="e.g. 19" min="16" max="60"
                 value="<?= htmlspecialchars($age ?? '') ?>" required/>
        </div>
        <div class="col-md-8">
          <label class="form-label">Department <span class="req">*</span></label>
          <select name="department_id" class="form-select" required>
            <option value="">— Choose your department —</option>
            <?php foreach ($deptList as $d): ?>
              <option value="<?= $d['id'] ?>" <?= (isset($deptId) && $deptId == $d['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <small class="text-muted" style="font-size:.74rem;">You will be placed in a class within this department after approval.</small>
        </div>
      </div>

      <hr class="divider"/>

      <h6 class="sec-title"><i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Upload Your Documents</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">A-Level Result Slip <span class="req">*</span></label>
          <span class="u-badge u-req">Required</span>
          <div class="upload-zone mt-1" id="z1">
            <input type="file" name="alevel_slip" accept=".jpg,.jpeg,.png,.pdf" onchange="showFile(this,'z1','f1')"/>
            <i class="bi bi-file-earmark-text"></i>
            <p>Click or drag your A-Level slip here<br><small>JPG, PNG or PDF · Max 5MB</small></p>
            <p class="fname" id="f1"></p>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Birth Certificate <span class="req">*</span></label>
          <span class="u-badge u-req">Required</span>
          <div class="upload-zone mt-1" id="z2">
            <input type="file" name="birth_certificate" accept=".jpg,.jpeg,.png,.pdf" onchange="showFile(this,'z2','f2')"/>
            <i class="bi bi-file-earmark-person"></i>
            <p>Click or drag your birth certificate here<br><small>JPG, PNG or PDF · Max 5MB</small></p>
            <p class="fname" id="f2"></p>
          </div>
        </div>
        <div class="col-12">
          <label class="form-label">National ID or Passport</label>
          <span class="u-badge u-opt">Optional</span>
          <div class="upload-zone mt-1" id="z3" style="max-width:360px;">
            <input type="file" name="id_document" accept=".jpg,.jpeg,.png,.pdf" onchange="showFile(this,'z3','f3')"/>
            <i class="bi bi-card-heading"></i>
            <p>Click or drag your ID or passport here<br><small>JPG, PNG or PDF · Max 5MB</small></p>
            <p class="fname" id="f3"></p>
          </div>
        </div>
      </div>

      <hr class="divider"/>

      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="agree" required/>
        <label class="form-check-label" for="agree" style="font-size:.84rem;">
          I confirm that all the information and documents I have provided are real and correct.
          I understand that fake documents will lead to disqualification.
        </label>
      </div>

      <button type="submit" class="btn-apply" id="applyBtn">
        <i class="bi bi-send-fill me-2"></i>Send My Application
      </button>
    </form>
  </div>
</div>

<footer>© <?= date('Y') ?> Our Education · All rights reserved</footer>
<script>
function showFile(inp, zoneId, nameId) {
  if (inp.files && inp.files[0]) {
    document.getElementById(nameId).textContent = '✓ ' + inp.files[0].name;
    const z = document.getElementById(zoneId);
    z.style.borderColor = '#2d6a4f'; z.style.background = '#f0fdf4';
  }
}
document.querySelectorAll('.upload-zone').forEach(z => {
  z.addEventListener('dragover',  e => { e.preventDefault(); z.classList.add('on'); });
  z.addEventListener('dragleave', ()  => z.classList.remove('on'));
  z.addEventListener('drop',      e  => { e.preventDefault(); z.classList.remove('on'); });
});
document.getElementById('regForm').addEventListener('submit', function(e) {
  if (!this.checkValidity()) { e.preventDefault(); this.classList.add('was-validated'); return; }
  this.classList.add('was-validated');
  const b = document.getElementById('applyBtn');
  b.disabled = true; b.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending…';
});
</script>
</body>
</html>
