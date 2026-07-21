<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Employees List</h2>
    <a href="index.php?route=employees-create" class="btn btn-primary">Add Employee</a>
</div>

<form class="row g-3 mb-4" method="GET" action="index.php">
    <input type="hidden" name="route" value="employees">
    <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="Search by name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <input type="text" name="department" class="form-control" placeholder="Search by department" value="<?= htmlspecialchars($_GET['department'] ?? '') ?>">
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-secondary">Search</button>
        <a href="index.php?route=employees" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<?php if (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"> <?= htmlspecialchars($_SESSION['error_message']) ?> </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<table class="table table-bordered bg-white shadow-sm">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Joining Date</th>
            <th>Total Leaves</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($employees as $emp): ?>
        <tr>
            <td><?= $emp['id'] ?></td>
            <td><?= htmlspecialchars($emp['employee_name']) ?></td>
            <td><?= htmlspecialchars($emp['email'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($emp['department_name'] ?? $emp['department'] ?? $emp['department_id'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($emp['joining_date'] ?? 'N/A') ?></td>
            <td><?= $emp['total_leaves'] ?? 0 ?></td>
            <td>
                <a href="index.php?route=employees-edit&id=<?= $emp['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="index.php?route=employees-delete&id=<?= $emp['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layout/footer.php'; ?>