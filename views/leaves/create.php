<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>Apply Leave</h2>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form action="/leaves-create" method="POST" class="card p-4 shadow-sm bg-white col-md-6">
    <!-- Foreign Key Reference Selection -->
    <div class="mb-3">
        <label class="form-label">Select Employee</label>
        <select name="employee_id" class="form-select" required>
            <option value="">-- Choose Employee --</option>
            <?php foreach ($employees as $emp): ?>
                <option value="<?= $emp['id'] ?>" <?= ($old['employee_id'] == $emp['id']) ? 'selected' : '' ?>><?= htmlspecialchars($emp['id'] . ' - ' . $emp['employee_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Leave Type</label>
        <select name="leave_type" class="form-select" required>
            <option value="">-- Choose Leave Type --</option>
            <option value="S" <?= ($old['leave_type'] === 'S') ? 'selected' : '' ?>>Sick</option>
            <option value="C" <?= ($old['leave_type'] === 'C') ? 'selected' : '' ?>>Casual</option>
            <option value="P" <?= ($old['leave_type'] === 'P') ? 'selected' : '' ?>>Paid</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Start Date</label>
        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($old['start_date']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">End Date</label>
        <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($old['end_date']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Reason</label>
        <textarea name="reason" class="form-control" rows="3" required><?= htmlspecialchars($old['reason']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-success">Submit Application</button>
</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>