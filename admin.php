<?php
include 'config.php';

// Create tables if they don't exist
$tables_sql = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        user_type ENUM('user', 'admin') DEFAULT 'user',
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS courses (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        course_name VARCHAR(255) NOT NULL,
        expiry_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS messages (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS login_activity (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11),
        login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables_sql as $sql) {
    mysqli_query($conn, $sql);
}

// Redirect if not admin (using email check)
if (!isset($_SESSION['email']) || strpos($_SESSION['email'], '@codecraft.com') === false) {
    header("Location: login.php");
    exit();
}

// Handle AJAX CRUD operations
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        // Users CRUD
        case 'update_user':
            $id = intval($_POST['id']);
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $type = mysqli_real_escape_string($conn, $_POST['user_type']);
            mysqli_query($conn, "UPDATE users SET name='$name', email='$email', user_type='$type' WHERE id='$id'");
            echo json_encode(['status'=>'updated']); 
            exit();

        case 'delete_user':
            $id = intval($_POST['id']);
            mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
            echo json_encode(['status'=>'deleted']); 
            exit();

        // Courses CRUD
        case 'add_course':
            $name = mysqli_real_escape_string($conn, $_POST['course_name']);
            $expiry = mysqli_real_escape_string($conn, $_POST['expiry_date']);
            mysqli_query($conn, "INSERT INTO courses (course_name, expiry_date) VALUES ('$name', '$expiry')");
            echo json_encode(['status'=>'added']); 
            exit();

        case 'update_course':
            $id = intval($_POST['id']);
            $name = mysqli_real_escape_string($conn, $_POST['course_name']);
            $expiry = mysqli_real_escape_string($conn, $_POST['expiry_date']);
            mysqli_query($conn, "UPDATE courses SET course_name='$name', expiry_date='$expiry' WHERE id='$id'");
            echo json_encode(['status'=>'updated']); 
            exit();

        case 'delete_course':
            $id = intval($_POST['id']);
            mysqli_query($conn, "DELETE FROM courses WHERE id='$id'");
            echo json_encode(['status'=>'deleted']); 
            exit();

        // Messages CRUD
        case 'send_message':
            $subject = mysqli_real_escape_string($conn, $_POST['subject']);
            $message = mysqli_real_escape_string($conn, $_POST['message']);
            mysqli_query($conn, "INSERT INTO messages (subject, message) VALUES ('$subject', '$message')");
            echo json_encode(['status'=>'sent']); 
            exit();

        case 'delete_message':
            $id = intval($_POST['id']);
            mysqli_query($conn, "DELETE FROM messages WHERE id='$id'");
            echo json_encode(['status'=>'deleted']); 
            exit();

        // Settings
        case 'update_profile':
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $pass = mysqli_real_escape_string($conn, $_POST['password']);
            $current_email = $_SESSION['email'];
            
            // Hash password if provided
            if (!empty($pass)) {
                $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE users SET name='$name', email='$email', password='$hashed_password' WHERE email='$current_email'");
            } else {
                mysqli_query($conn, "UPDATE users SET name='$name', email='$email' WHERE email='$current_email'");
            }
            
            $_SESSION['username'] = $name;
            $_SESSION['email'] = $email;
            echo json_encode(['status'=>'updated']); 
            exit();
    }
}

// Fetch data
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
$courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY id DESC");
$messages = mysqli_query($conn, "SELECT * FROM messages ORDER BY id DESC");
$user_count = mysqli_num_rows($users);
$course_count = mysqli_num_rows($courses);
$message_count = mysqli_num_rows($messages);

// Expired courses count
$expired = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses WHERE expiry_date < CURDATE()");
$expired_count = $expired ? mysqli_fetch_assoc($expired)['total'] : 0;

// Chart data - handle case when table doesn't exist yet
$chart_labels = [];
$chart_data = [];
$logins = mysqli_query($conn, "
    SELECT DATE(login_time) AS day, COUNT(*) AS count
    FROM login_activity
    WHERE login_time >= NOW() - INTERVAL 7 DAY
    GROUP BY day
    ORDER BY day ASC
");

if ($logins) {
    while ($r = mysqli_fetch_assoc($logins)) {
        $chart_labels[] = $r['day'];
        $chart_data[] = $r['count'];
    }
} else {
    // Default chart data if no login activity
    $chart_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $chart_data = [0, 0, 0, 0, 0, 0, 0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<style>
body {background:#f4f6f8; font-family:'Segoe UI'; display:flex;}
.sidebar {width:230px; background:#0d6efd; color:#fff; height:100vh; padding:20px; position:fixed;}
.sidebar h2 {text-align:center; margin-bottom:20px;}
.sidebar ul {list-style:none; padding:0;}
.sidebar ul li {margin:10px 0;}
.sidebar ul li a {color:#fff; text-decoration:none; display:block; padding:10px; border-radius:5px;}
.sidebar ul li a:hover, .sidebar ul li a.active {background:#0b5ed7;}
.main-content {margin-left:250px; padding:20px; width:100%;}
.navbar {background:#fff; padding:10px 20px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);}
.info-card {background:#fff; padding:20px; border-radius:10px; text-align:center; box-shadow:0 2px 4px rgba(0,0,0,0.1);}
.info-card i {font-size: 2.5rem; margin-bottom: 10px; color:#0d6efd;}
.toast {position:fixed; bottom:20px; right:20px; z-index:9999;}
.section {display:none;}
.section.active {display:block;}
</style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="#dashboard" class="active"><i class="fa fa-chart-line"></i> Dashboard</a></li>
        <li><a href="#accounts"><i class="fa fa-users"></i> Users</a></li>
        <li><a href="#courses"><i class="fa fa-book"></i> Courses</a></li>
        <li><a href="#messages"><i class="fa fa-envelope"></i> Messages</a></li>
        <li><a href="#settings"><i class="fa fa-cog"></i> Settings</a></li>
        <li><a href="#help"><i class="fa fa-question-circle"></i> Help</a></li>
        <li><a href="logout.php" id="logoutBtn"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
<nav class="navbar mb-4">
    <h4>Welcome, <span><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span></h4>
</nav>

<!-- DASHBOARD -->
<div id="dashboard" class="section active">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="info-card">
                <i class="fa fa-users"></i>
                <h5>Total Users</h5>
                <h2><?= $user_count ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card">
                <i class="fa fa-book"></i>
                <h5>Total Courses</h5>
                <h2><?= $course_count ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card">
                <i class="fa fa-envelope"></i>
                <h5>Total Messages</h5>
                <h2><?= $message_count ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card">
                <i class="fa fa-calendar-times"></i>
                <h5>Expired Courses</h5>
                <h2><?= $expired_count ?></h2>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-body">
            <h5>User Login Activity</h5>
            <canvas id="userChart"></canvas>
        </div>
    </div>
</div>

<!-- USERS -->
<div id="accounts" class="section">
    <div class="card">
        <div class="card-body">
            <h5>Manage Users</h5>
            <table class="table table-striped mt-3">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php 
                if ($users && mysqli_num_rows($users) > 0) {
                    mysqli_data_seek($users, 0); // Reset pointer
                    while ($u = mysqli_fetch_assoc($users)) { ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><input class="form-control nameInput" value="<?= htmlspecialchars($u['name'] ?? '') ?>"></td>
                        <td><input class="form-control emailInput" value="<?= htmlspecialchars($u['email'] ?? '') ?>"></td>
                        <td>
                            <select class="form-control userTypeSelect">
                                <option value="user" <?= ($u['user_type'] ?? '')=='user'?'selected':'' ?>>User</option>
                                <option value="admin" <?= ($u['user_type'] ?? '')=='admin'?'selected':'' ?>>Admin</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info viewDetailsBtn" data-id="<?= $u['id'] ?>" data-name="<?= htmlspecialchars($u['name'] ?? '') ?>" data-email="<?= htmlspecialchars($u['email'] ?? '') ?>" data-type="<?= $u['user_type'] ?? '' ?>">View</button>
                            <button class="btn btn-sm btn-primary updateUser" data-id="<?= $u['id'] ?>">Update</button>
                            <button class="btn btn-sm btn-danger deleteUser" data-id="<?= $u['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php } 
                } else { ?>
                    <tr>
                        <td colspan="5" class="text-center">No users found</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- COURSES -->
<div id="courses" class="section">
    <div class="card mb-4">
        <div class="card-body">
            <h5>Add Course</h5>
            <div class="row">
                <div class="col-md-4"><input type="text" id="course_name" class="form-control" placeholder="Course name"></div>
                <div class="col-md-4"><input type="date" id="expiry_date" class="form-control"></div>
                <div class="col-md-4"><button id="addCourse" class="btn btn-success w-100">Add Course</button></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Course List</h5>
            <table class="table table-striped">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Expiry</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php 
                if ($courses && mysqli_num_rows($courses) > 0) {
                    mysqli_data_seek($courses, 0); // Reset pointer
                    while ($c = mysqli_fetch_assoc($courses)) {
                        $expired = strtotime($c['expiry_date']) < time();
                ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><input class="form-control" value="<?= htmlspecialchars($c['course_name']) ?>"></td>
                        <td><input type="date" class="form-control" value="<?= $c['expiry_date'] ?>"></td>
                        <td><span class="badge bg-<?= $expired ? 'danger' : 'success' ?>"><?= $expired ? 'Expired' : 'Active' ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-primary updateCourse" data-id="<?= $c['id'] ?>">Update</button>
                            <button class="btn btn-sm btn-danger deleteCourse" data-id="<?= $c['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php } 
                } else { ?>
                    <tr>
                        <td colspan="5" class="text-center">No courses found</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MESSAGES -->
<div id="messages" class="section">
    <div class="card mb-4">
        <div class="card-body">
            <h5>Send Message</h5>
            <input type="text" id="subject" class="form-control mb-2" placeholder="Subject">
            <textarea id="message" class="form-control mb-2" rows="4" placeholder="Type message..."></textarea>
            <button id="sendMessage" class="btn btn-primary">Send</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Inbox</h5>
            <table class="table table-striped">
                <thead>
                    <tr><th>ID</th><th>Subject</th><th>Message</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php 
                if ($messages && mysqli_num_rows($messages) > 0) {
                    mysqli_data_seek($messages, 0); // Reset pointer
                    while ($m = mysqli_fetch_assoc($messages)) { ?>
                    <tr>
                        <td><?= $m['id'] ?></td>
                        <td><?= htmlspecialchars($m['subject']) ?></td>
                        <td><?= htmlspecialchars($m['message']) ?></td>
                        <td><?= $m['created_at'] ?></td>
                        <td><button class="btn btn-sm btn-danger deleteMessage" data-id="<?= $m['id'] ?>">Delete</button></td>
                    </tr>
                <?php } 
                } else { ?>
                    <tr>
                        <td colspan="5" class="text-center">No messages found</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SETTINGS -->
<div id="settings" class="section">
    <div class="card">
        <div class="card-body">
            <h5>Update Profile</h5>
            <input type="text" id="admin_name" class="form-control mb-2" placeholder="New Name" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">
            <input type="email" id="admin_email" class="form-control mb-2" placeholder="New Email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>">
            <input type="password" id="admin_password" class="form-control mb-2" placeholder="New Password (leave blank to keep current)">
            <button id="updateProfile" class="btn btn-success">Update Profile</button>
        </div>
    </div>
</div>

<!-- HELP -->
<div id="help" class="section">
    <div class="card">
        <div class="card-body">
            <h5>Help & Support</h5>
            <p>💡 <b>Manage Users</b> — Add, edit, or remove user accounts.</p>
            <p>📚 <b>Courses</b> — Add new courses and set expiry dates.</p>
            <p>✉ <b>Messages</b> — Send announcements to users.</p>
            <p>⚙ <b>Settings</b> — Update your admin profile info.</p>
            <p>📞 Need help? Contact support@example.com</p>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="userDetailsModalLabel">User Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><b>ID:</b> <span id="detailId"></span></p>
        <p><b>Name:</b> <span id="detailName"></span></p>
        <p><b>Email:</b> <span id="detailEmail"></span></p>
        <p><b>User Type:</b> <span id="detailType"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast align-items-center text-white bg-success border-0" id="toast" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="d-flex">
    <div class="toast-body" id="toastBody">Action completed</div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const toastEl = document.getElementById('toast');
const toast = new bootstrap.Toast(toastEl);

function showToast(message, type='success') {
    const toastBody = document.getElementById('toastBody');
    toastBody.textContent = message;
    toastEl.className = 'toast align-items-center text-white border-0';
    toastEl.classList.add(type === 'success' ? 'bg-success' : 'bg-danger');
    toast.show();
}

// Navigation handling
document.querySelectorAll('.sidebar ul li a').forEach(a => {
    a.addEventListener('click', e => {
        // Only prevent default for navigation links, not logout
        if (!a.getAttribute('href').includes('logout.php')) {
            e.preventDefault();
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.sidebar ul li a').forEach(link => link.classList.remove('active'));
            const target = a.getAttribute('href').substring(1);
            document.getElementById(target).classList.add('active');
            a.classList.add('active');
        }
    });
});

// User CRUD actions
document.querySelectorAll('.updateUser').forEach(button => {
    button.addEventListener('click', async () => {
        const tr = button.closest('tr');
        const id = button.dataset.id;
        const name = tr.querySelector('.nameInput').value.trim();
        const email = tr.querySelector('.emailInput').value.trim();
        const user_type = tr.querySelector('.userTypeSelect').value;
        const res = await fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'update_user',
                id, name, email, user_type
            })
        });
        const data = await res.json();
        if (data.status === 'updated') showToast('User updated successfully');
        else showToast('Error updating user', 'error');
    });
});

document.querySelectorAll('.deleteUser').forEach(button => {
    button.addEventListener('click', async () => {
        if (!confirm('Delete this user?')) return;
        const id = button.dataset.id;
        const res = await fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'delete_user',
                id
            })
        });
        const data = await res.json();
        if (data.status === 'deleted') {
            button.closest('tr').remove();
            showToast('User deleted successfully');
        } else showToast('Error deleting user', 'error');
    });
});

// View User Details modal
const userDetailsModal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
document.querySelectorAll('.viewDetailsBtn').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('detailId').textContent = button.dataset.id;
        document.getElementById('detailName').textContent = button.dataset.name;
        document.getElementById('detailEmail').textContent = button.dataset.email;
        document.getElementById('detailType').textContent = button.dataset.type;
        userDetailsModal.show();
    });
});

// Add Course
document.getElementById('addCourse').addEventListener('click', async () => {
    const name = document.getElementById('course_name').value.trim();
    const expiry = document.getElementById('expiry_date').value;
    if (!name || !expiry) return showToast('Please enter course name and expiry date', 'error');

    const res = await fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'add_course',
            course_name: name,
            expiry_date: expiry
        })
    });
    const data = await res.json();
    if (data.status === 'added') {
        showToast('Course added!');
        location.reload();
    } else showToast('Error adding course', 'error');
});

// Update Course
document.querySelectorAll('.updateCourse').forEach(button => {
    button.addEventListener('click', async () => {
        const tr = button.closest('tr');
        const id = button.dataset.id;
        const course_name = tr.querySelector('input[type=text]').value.trim();
        const expiry_date = tr.querySelector('input[type=date]').value;
        if (!course_name || !expiry_date) return showToast('Fill all course fields', 'error');

        const res = await fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'update_course',
                id, course_name, expiry_date
            })
        });
        const data = await res.json();
        if (data.status === 'updated') showToast('Course updated!');
        else showToast('Error updating course', 'error');
    });
});

// Delete Course
document.querySelectorAll('.deleteCourse').forEach(button => {
    button.addEventListener('click', async () => {
        if (!confirm('Delete this course?')) return;
        const id = button.dataset.id;
        const res = await fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'delete_course',
                id
            })
        });
        const data = await res.json();
        if (data.status === 'deleted') {
            button.closest('tr').remove();
            showToast('Course deleted!');
        } else showToast('Error deleting course', 'error');
    });
});

// Send Message
document.getElementById('sendMessage').addEventListener('click', async () => {
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    if (!subject || !message) return showToast('Please fill subject and message', 'error');

    const res = await fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'send_message',
            subject, message
        })
    });
    const data = await res.json();
    if (data.status === 'sent') {
        showToast('Message sent!');
        location.reload();
    } else showToast('Error sending message', 'error');
});

// Delete Message
document.querySelectorAll('.deleteMessage').forEach(button => {
    button.addEventListener('click', async () => {
        if (!confirm('Delete this message?')) return;
        const id = button.dataset.id;
        const res = await fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'delete_message',
                id
            })
        });
        const data = await res.json();
        if (data.status === 'deleted') {
            button.closest('tr').remove();
            showToast('Message deleted!');
        } else showToast('Error deleting message', 'error');
    });
});

// Update Profile
document.getElementById('updateProfile').addEventListener('click', async () => {
    const name = document.getElementById('admin_name').value.trim();
    const email = document.getElementById('admin_email').value.trim();
    const password = document.getElementById('admin_password').value.trim();

    if (!name || !email) return showToast('Name and email are required', 'error');

    const res = await fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'update_profile',
            name, email, password
        })
    });
    const data = await res.json();
    if (data.status === 'updated') showToast('Profile updated! Reloading...');
    else showToast('Error updating profile', 'error');
    setTimeout(() => location.reload(), 1500);
});

// Chart
const ctx = document.getElementById('userChart').getContext('2d');
const userChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_labels) ?>,
        datasets: [{
            label: 'Logins per Day',
            data: <?= json_encode($chart_data) ?>,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: 'Date' } },
            y: { title: { display: true, text: 'Number of Logins' }, beginAtZero: true }
        }
    }
});
</script>

</div>

</body>
</html>