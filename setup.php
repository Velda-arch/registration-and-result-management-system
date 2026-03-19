<?php
if (($_GET['key'] ?? '') !== 'OE2025') { http_response_code(403); die('Access denied. Use ?key=OE2025'); }
require_once __DIR__ . '/includes/config.php';
$db = getDB();

// Create all tables
$db->query("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(150) NOT NULL, email VARCHAR(150) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, role ENUM('admin','lecturer') NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$db->query("CREATE TABLE IF NOT EXISTS departments (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(150) NOT NULL UNIQUE, description TEXT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$db->query("CREATE TABLE IF NOT EXISTS classes (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(150) NOT NULL, department_id INT NOT NULL, academic_year VARCHAR(20) NOT NULL DEFAULT '2025/2026', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$db->query("CREATE TABLE IF NOT EXISTS courses (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(150) NOT NULL, code VARCHAR(30) DEFAULT NULL, class_id INT NOT NULL, lecturer_id INT DEFAULT NULL, semester TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE, FOREIGN KEY (lecturer_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$db->query("CREATE TABLE IF NOT EXISTS students (id INT AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(150) NOT NULL, email VARCHAR(150) NOT NULL UNIQUE, age INT NOT NULL, department_id INT DEFAULT NULL, class_id INT DEFAULT NULL, alevel_slip VARCHAR(255) DEFAULT NULL, birth_certificate VARCHAR(255) DEFAULT NULL, id_document VARCHAR(255) DEFAULT NULL, status ENUM('pending','approved','rejected') DEFAULT 'pending', rejection_reason TEXT DEFAULT NULL, registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL, FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$db->query("CREATE TABLE IF NOT EXISTS results (id INT AUTO_INCREMENT PRIMARY KEY, student_id INT NOT NULL, course_id INT NOT NULL, lecturer_id INT NOT NULL, semester TINYINT NOT NULL DEFAULT 1, score DECIMAL(5,2) NOT NULL, grade VARCHAR(5) DEFAULT NULL, remarks TEXT DEFAULT NULL, sent_to_student TINYINT(1) DEFAULT 0, submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unique_result (student_id, course_id, semester), FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE, FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE, FOREIGN KEY (lecturer_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$db->query("CREATE TABLE IF NOT EXISTS email_log (id INT AUTO_INCREMENT PRIMARY KEY, student_id INT NOT NULL, semester TINYINT DEFAULT NULL, class_id INT DEFAULT NULL, sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, status VARCHAR(50) DEFAULT 'sent', FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Sample departments
$db->query("INSERT IGNORE INTO departments (name,description) VALUES ('Science & Technology','Physics, Chemistry, Biology and Computer Science'),('Arts & Humanities','Literature, History, Philosophy and Languages'),('Business & Economics','Accounting, Finance, Management and Economics'),('Medicine & Health','Nursing, Public Health and Medical Sciences'),('Engineering','Civil, Mechanical and Electrical Engineering')");

// Upload dirs
foreach (['alevel_slip','birth_certificate','id_document'] as $d) {
    $p = __DIR__ . '/assets/uploads/' . $d . '/';
    if (!is_dir($p)) mkdir($p, 0755, true);
}

// Default users
$users = [
    ['System Administrator', 'admin@oureducation.edu', 'Admin@1234', 'admin'],
    ['Dr. John Smith',       'dr.smith@oureducation.edu', 'Lect@1234', 'lecturer'],
    ['Dr. Amina Jones',      'dr.jones@oureducation.edu', 'Lect@1234', 'lecturer'],
    ['Dr. Emeka Ngozi',      'dr.ngozi@oureducation.edu', 'Lect@1234', 'lecturer'],
];
$rows = '';
foreach ($users as [$name, $email, $plain, $role]) {
    $hash = password_hash($plain, PASSWORD_BCRYPT, ['cost'=>10]);
    $ck = $db->prepare("SELECT id FROM users WHERE email=?");
    $ck->bind_param("s",$email); $ck->execute(); $ck->store_result();
    if ($ck->num_rows > 0) {
        $upd = $db->prepare("UPDATE users SET password=?,full_name=? WHERE email=?");
        $upd->bind_param("sss",$hash,$name,$email); $upd->execute(); $upd->close();
        $tag = '<span style="background:#fef9c3;padding:2px 9px;border-radius:4px;font-size:.8rem;">⟳ Updated</span>';
    } else {
        $ins = $db->prepare("INSERT INTO users (full_name,email,password,role) VALUES (?,?,?,?)");
        $ins->bind_param("ssss",$name,$email,$hash,$role); $ins->execute(); $ins->close();
        $tag = '<span style="background:#dcfce7;padding:2px 9px;border-radius:4px;font-size:.8rem;">✅ Created</span>';
    }
    $ck->close();
    $rows .= "<tr><td>$tag</td><td>$name</td><td>$email</td><td><code>$plain</code></td><td>".ucfirst($role)."</td></tr>";
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"/>
<title>Setup — Our Education</title>
<style>body{font-family:'DM Sans',sans-serif;background:#f0f4f8;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:30px;}
.card{background:#fff;border-radius:14px;padding:40px;max-width:720px;width:100%;box-shadow:0 6px 30px rgba(0,0,0,.1);}
h1{font-family:Georgia,serif;color:#1e3a5f;font-size:1.7rem;}
table{width:100%;border-collapse:collapse;margin:16px 0;font-size:.86rem;}
th{background:#1e3a5f;color:#fff;padding:10px 14px;text-align:left;}
td{padding:10px 14px;border-bottom:1px solid #e5e7eb;}
.warn{background:#fef9c3;border:1px solid #fde047;border-radius:9px;padding:13px 17px;font-size:.85rem;color:#713f12;margin-top:16px;}
.btn-go{background:linear-gradient(135deg,#1e3a5f,#2d6a4f);color:#fff;border:none;border-radius:9px;padding:11px 26px;font-weight:600;text-decoration:none;display:inline-block;margin-top:16px;margin-right:8px;font-size:.9rem;}
.btn-go:hover{opacity:.9;color:#fff;}
</style>  <link rel="stylesheet" href="assets/css/bootstrap.css"/>
  <link rel="stylesheet" href="assets/css/icons.css"/>
  <script src="assets/js/bootstrap.bundle.js" defer></script>
</head>
<body><div class="card">
<h1>🎓 Setup Complete!</h1>
<p style="color:#6b7280;">All tables created and default accounts are ready.</p>
<table><thead><tr><th>Status</th><th>Name</th><th>Email</th><th>Password</th><th>Role</th></tr></thead>
<tbody><?=$rows?></tbody></table>
<div class="warn">⚠️ <strong>Important:</strong> Delete <code>setup.php</code> after this. Never leave it on a live server.</div>
<div>
  <a href="login.php?ref=OE2025" class="btn-go">🔐 Go to Login</a>
  <a href="register.php" class="btn-go" style="background:linear-gradient(135deg,#2d6a4f,#1a4535);">📝 Student Registration</a>
</div>
</div></body></html>
