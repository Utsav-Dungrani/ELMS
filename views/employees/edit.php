<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>Edit Employee</h2>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form action="/employees-edit?id=<?= $employee['id'] ?>" method="POST" class="card p-4 shadow-sm bg-white col-md-6">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($employee['employee_name'] ?? '') ?>" class="form-control" required>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($employee['email'] ?? '') ?>" class="form-control" required>
        <label class="form-label">Department</label>
        <select name="department_id" class="form-select" required>
            <option value="">-- Choose department --</option>
            <?php foreach ($stmt as $dept): ?>
                <option value="<?= $dept['id'] ?>" <?= ((int)($employee['department_id'] ?? 0) === (int)$dept['id']) ? 'selected' : '' ?>><?= htmlspecialchars($dept['department_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label class="form-label">Joining date</label>
        <input type="date" name="joining_date" value="<?= htmlspecialchars($employee['joining_date'] ?? '') ?>" class="form-control" required>

        <label class="form-label">Password</label>
        <div class="input-group">
            <input type="password" class="form-control" id="password" name="password" value="<?= htmlspecialchars($employee['password'] ?? '') ?>" required>
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)" >
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success">Update Employee</button>
        <a href="/employees" class="btn btn-outline-secondary">Back</a>
    </div>
</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>