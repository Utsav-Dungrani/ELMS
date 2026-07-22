<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Apply Leave</h2>
        <p class="text-muted small mb-0">Submit a new leave request for your account.</p>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form action="/employee-leaves-create" method="POST" class="card p-4 shadow-sm bg-white col-md-8">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <div class="mb-3">
        <label class="form-label">Leave Type</label>
        <select name="leave_type" class="form-select" required>
            <option value="">-- Choose Leave Type --</option>
            <option value="S" <?= ($old['leave_type'] === 'Sick') ? 'selected' : '' ?>>Sick</option>
            <option value="C" <?= ($old['leave_type'] === 'Casual') ? 'selected' : '' ?>>Casual</option>
            <option value="P" <?= ($old['leave_type'] === 'Paid') ? 'selected' : '' ?>>Paid</option>
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
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success">Submit Leave</button>
        <a href="/employee-leaves" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>