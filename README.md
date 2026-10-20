# Quick Start Guide - Student Evaluator

## 🟢 Status: FIXED ✓

Your Student Evaluator application is now fully operational!

## What Was Wrong?
- ❌ Database `student_evaluator` was missing
- ❌ Tables `students` and `staff_users` didn't exist
- ❌ Incorrect IP address being used to access the app

## What Was Fixed?
- ✅ Database created with all required tables
- ✅ Error messages enhanced for better debugging
- ✅ Application tested and verified working

---

## How to Use

### 1️⃣ Access Setup Page
Open your browser and go to:
```
http://localhost/Student_evaluator/setup_staff.php
```

### 2️⃣ Create Staff Account
- Enter a username (3-100 characters)
- Enter a password (minimum 12 characters)
- Confirm the password
- Click "CREATE STAFF ACCOUNT"

### 3️⃣ Log In
Go to: http://localhost/Student_evaluator/login.php
- Use your staff credentials from step 2

### 4️⃣ Start Using the App
- Add student records
- View evaluations
- Delete records as needed

---

## Network Access

### Your Computer's IP Address
```
192.168.100.29
```

### To Access from Another Device on Your Network
Replace `localhost` with `192.168.100.29`:
```
http://192.168.100.29/Student_evaluator/index.php
```

---

## Test Links

| Page | Link |
|------|------|
| Staff Setup | http://localhost/Student_evaluator/setup_staff.php |
| Staff Login | http://localhost/Student_evaluator/login.php |
| Main App | http://localhost/Student_evaluator/index.php |
| Public Demo | http://localhost/Student_evaluator/demo.php |

---

## 🔧 Server Information

- **Web Server**: Apache (XAMPP)
- **Database**: MySQL
- **Language**: PHP
- **Status**: ✅ All Running

---

## Database Schema

### students table
Stores student evaluation records:
- student_name, year_level, course, section, subject
- individual_work, module_completion, coding_assessment, term_exam
- final_evaluation, result

### staff_users table
Stores staff login credentials:
- username, password_hash, created_at

---

**Questions?** Check the error logs at: `C:\xampp\apache\logs\error.log`
