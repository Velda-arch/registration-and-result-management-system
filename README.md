# 🎓 Our Education — School Management System

A complete PHP + MySQL school portal built with Bootstrap 5.
Fully responsive, professional, and user-friendly.

---

## 📁 Project Structure

```
our_education/
│
├── index.php                  ← Public homepage
├── register.php               ← Student registration (public)
├── login.php                  ← Staff login (secret URL only)
├── logout.php                 ← Signs out and redirects
├── setup.php                  ← Run ONCE to create tables + users
│
├── admin/
│   └── dashboard.php          ← Full admin panel
│
├── lecturer/
│   └── dashboard.php          ← Lecturer panel
│
├── includes/
│   ├── config.php             ← Database settings + helpers
│   ├── mailer.php             ← Email sender
│   └── styles.php             ← Shared CSS for dashboards
│
├── assets/
│   └── uploads/               ← Student documents
│       ├── alevel_slip/
│       ├── birth_certificate/
│       └── id_document/
│
└── database.sql               ← Full database schema
```

---

## ⚙️ Setup — Step by Step

### Step 1 — Import the database
Open **phpMyAdmin**, click **Import**, and select `database.sql`.
This creates the `school_system` database and all tables.

### Step 2 — Edit config.php
Open `includes/config.php` and change:
```php
define('DB_USER', 'root');       // Your MySQL username
define('DB_PASS', '');           // Your MySQL password (blank for XAMPP default)
define('DB_NAME', 'school_system');

// For sending real emails (Gmail):
define('SMTP_USER', 'your_gmail@gmail.com');
define('SMTP_PASS', 'your_gmail_app_password');
```

### Step 3 — Place folder in XAMPP
Put the `our_education/` folder inside:
```
C:\xampp\htdocs\our_education\
```

### Step 4 — Run setup.php ONCE
Visit this URL in your browser:
```
http://localhost/our_education/setup.php?key=OE2025
```
This creates all user accounts with correct passwords on YOUR server.
**Delete setup.php after running it.**

### Step 5 — You're ready!
- Student page:  `http://localhost/our_education/register.php`
- Staff login:   `http://localhost/our_education/login.php?ref=OE2025`
- Homepage:      `http://localhost/our_education/index.php`

---

## 🔐 Default Login Credentials

> Staff login URL: `http://localhost/our_education/login.php?ref=OE2025`
> (Without `?ref=OE2025` → redirected to student registration)

| Role       | Email                        | Password    |
|------------|------------------------------|-------------|
| **Admin**  | admin@oureducation.edu       | `Admin@1234`|
| Lecturer 1 | dr.smith@oureducation.edu    | `Lect@1234` |
| Lecturer 2 | dr.jones@oureducation.edu    | `Lect@1234` |
| Lecturer 3 | dr.ngozi@oureducation.edu    | `Lect@1234` |

**Change these passwords after your first login!**

---

## 🌊 How the System Works

```
Student visits register.php
  → Fills name, email, age, picks department
  → Uploads A-Level slip + birth certificate (+ optional ID)
  → Application saved as PENDING

Admin logs in → sees pending applications
  → Reviews documents → clicks Approve or Reject
  → On Approve: student is auto-placed in the class
    that matches their department
  → Admin can also move student to a different class

Admin sets up:
  → Departments (already have 5 samples)
  → Classes (e.g. Science Year 1, Science Year 2)
  → Courses (assigned to a class + semester 1 or 2)
  → Assigns a lecturer to each course

Lecturer logs in → sees their classes
  → Clicks a class → picks Semester 1 or 2
  → Sees all students in that class
  → For their own courses: enters marks (0-100)
  → Other lecturers' courses are locked (greyed out)
  → If any student is missing a mark → red warning shown
  → When all marks filled → admin is notified

Admin goes to Results & Emails tab
  → Filters by Class + Semester
  → Sees all students + all their course marks
  → Can send results to ONE student or ALL students
  → Each student only receives their own results
  → Before sending: missing marks are flagged
```

---

## 📧 Setting Up Real Emails (Gmail)

1. Go to your Google Account → **Security** → **2-Step Verification** (enable it)
2. Then go to **App Passwords** → create one for "Mail"
3. Copy the 16-character password
4. Paste it in `includes/config.php` as `SMTP_PASS`
5. Set `SMTP_USER` to your Gmail address

---

## 🎨 Colors Used

| Color  | Hex       | Used For                     |
|--------|-----------|------------------------------|
| Navy   | `#1e3a5f` | Sidebar, headers, main text  |
| Green  | `#2d6a4f` | Buttons, approvals, accents  |
| Gold   | `#c9a84c` | Highlights, logos, badges    |

---

## 🔒 Security Features

- Passwords hashed with `bcrypt`
- Staff login hidden behind secret URL key `?ref=OE2025`
- Students who visit `/login.php` without the key are sent to registration
- All inputs sanitized against XSS
- SQL queries use prepared statements
- File uploads validated by type and size
- Each lecturer can only edit their own course marks
- Each student only receives their own results by email
