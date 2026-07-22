<?php
$isEmployee = isset($_GET['tab']) && $_GET['tab'] === 'employee';
?>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center vh-100">
<div class="container col-md-5">
    <div class="card p-4 shadow-sm">
        <h3 class="text-center mb-3">Welcome to ELMS</h3>
        <p class="text-center text-muted mb-4">Choose whether you are logging in as an admin or an employee.</p>

        <div class="btn-group w-100 mb-4" role="group" aria-label="Login type toggle">
            <button type="button" class="btn btn-outline-primary <?= !$isEmployee ? 'active' : '' ?>" id="adminTabBtn">Admin Login</button>
            <button type="button" class="btn btn-outline-primary <?= $isEmployee ? 'active' : '' ?>" id="employeeTabBtn"> Employee Login</button>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">Invalid credentials!</div>
        <?php endif; ?>

        <div id="adminLoginForm" <?= $isEmployee ? 'style="display:none;"' : '' ?>>
            <form action="/do-login" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="admin" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="admin123" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login as Admin</button>
            </form>
        </div>

        <div id="employeeLoginForm" <?= $isEmployee ? '' : 'style="display:none;"' ?>>
            <form action="/employee-do-login" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login as Employee</button>
            </form>
        </div>
    </div>
</div>

<script>
    const adminTabBtn = document.getElementById('adminTabBtn');
    const employeeTabBtn = document.getElementById('employeeTabBtn');
    const adminLoginForm = document.getElementById('adminLoginForm');
    const employeeLoginForm = document.getElementById('employeeLoginForm');

    adminTabBtn.addEventListener('click', function () {
        adminTabBtn.classList.add('active');
        employeeTabBtn.classList.remove('active');
        adminLoginForm.style.display = 'block';
        employeeLoginForm.style.display = 'none';
    });

    employeeTabBtn.addEventListener('click', function () {
        employeeTabBtn.classList.add('active');
        adminTabBtn.classList.remove('active');
        employeeLoginForm.style.display = 'block';
        adminLoginForm.style.display = 'none';
    });
</script>
</body>
</html>