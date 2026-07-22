<html>
<head>
    <title>Employee Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center vh-100">
<div class="container col-md-4">
    <div class="card p-4 shadow-sm">
        <h3 class="text-center mb-3">Employee Login</h3>
        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger">Invalid employee credentials!</div>
        <?php endif; ?>
        <form action="/employee-do-login" method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>
