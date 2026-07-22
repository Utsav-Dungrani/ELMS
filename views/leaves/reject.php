<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>Reject Leave Request</h2>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card p-4 shadow-sm bg-white col-md-8 mb-3">
    <h5 class="mb-3">Leave Details</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="text-muted small">Employee</div>
            <div class="fw-semibold"><?= htmlspecialchars($leave['employee_name'] ?? 'N/A') ?></div>
        </div>
        <div class="col-md-6">
            <div class="text-muted small">Leave Type</div>
            <div class="fw-semibold"><?= htmlspecialchars($leave['leave_type'] ?? '') ?></div>
        </div>
        <div class="col-md-6">
            <div class="text-muted small">Start Date</div>
            <div class="fw-semibold"><?= htmlspecialchars($leave['start_date'] ?? '') ?></div>
        </div>
        <div class="col-md-6">
            <div class="text-muted small">End Date</div>
            <div class="fw-semibold"><?= htmlspecialchars($leave['end_date'] ?? '') ?></div>
        </div>
        <div class="col-12">
            <div class="text-muted small">Application Reason</div>
            <div class="fw-semibold"><?= htmlspecialchars($leave['reason'] ?? '') ?></div>
        </div>
    </div>
</div>

<form action="/leaves-reject" method="POST" class="card p-4 shadow-sm bg-white col-md-8">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="id" value="<?= (int) $leave['id'] ?>">
    <div class="mb-3">
        <label class="form-label">Rejection Reason</label>
        <textarea name="rejection_reason" class="form-control" rows="4" maxlength="500" required><?= htmlspecialchars($rejectionReason ?? '') ?></textarea>
        <div class="form-text">
            Explain why this leave request is being rejected.
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-danger">Reject Leave</button>
        <a href="/leaves" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<?php include __DIR__ . '/../layout/footer.php'; ?>
