<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Employee Dashboard</h2>
        <p class="text-muted small mb-0">Welcome back, <?= htmlspecialchars($employee['employee_name'] ?? 'Employee') ?>.</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Employee Details</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted small">Name</div>
                <div class="fw-semibold"><?= htmlspecialchars($employee['employee_name'] ?? 'N/A') ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Email</div>
                <div class="fw-semibold"><?= htmlspecialchars($employee['email'] ?? 'N/A') ?></div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Department</div>
                <div class="fw-semibold"><?= htmlspecialchars($employee['department_name'] ?? 'N/A') ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Approved</div>
                <h3 class="fw-bold mb-0 text-success"><?= (int) ($stats['approved'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Pending</div>
                <h3 class="fw-bold mb-0 text-warning"><?= (int) ($stats['pending'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Rejected</div>
                <h3 class="fw-bold mb-0 text-danger"><?= (int) ($stats['rejected'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total Requests</div>
                <h3 class="fw-bold mb-0"><?= (int) ($stats['total'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">My Leave Requests</h5>
            <a href="/employee-leaves" class="btn btn-primary btn-sm">Apply Leave</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($employeeLeaves)): ?>
                    <?php foreach ($employeeLeaves as $index => $leave): ?>
                        <tr>
                            <td><?= (int) (($pagination['page'] - 1) * $pagination['limit'] + $index + 1) ?></td>
                            <td><?= htmlspecialchars($leave['leave_type'] ?? '') ?></td>
                            <td><?= htmlspecialchars($leave['start_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($leave['end_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($leave['status'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">No leave requests yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (($pagination['totalPages'] ?? 1) > 1): ?>
            <nav aria-label="Employee dashboard pagination" class="mt-3">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= ($pagination['page'] <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="/employee-dashboard?page=<?= max(1, $pagination['page'] - 1) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                        <li class="page-item <?= ($i === $pagination['page']) ? 'active' : '' ?>">
                            <a class="page-link" href="/employee-dashboard?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($pagination['page'] >= $pagination['totalPages']) ? 'disabled' : '' ?>">
                        <a class="page-link" href="/employee-dashboard?page=<?= min($pagination['totalPages'], $pagination['page'] + 1) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
