<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">My Leave Requests</h2>
        <p class="text-muted small mb-0">View detailed leave history and submit new leave requests.</p>
    </div>
    <a href="/employee-leaves-create" class="btn btn-primary btn-sm">Apply Leave</a>
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

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row mb-3">
            <form method="GET" action="/employee-leaves" class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Leave Type</label>
                    <select name="leave_type" class="form-select">
                        <option value="">All</option>
                        <option value="Sick" <?= ($_GET['leave_type'] ?? '') === 'Sick' ? 'selected' : '' ?>>Sick</option>
                        <option value="Casual" <?= ($_GET['leave_type'] ?? '') === 'Casual' ? 'selected' : '' ?>>Casual</option>
                        <option value="Paid" <?= ($_GET['leave_type'] ?? '') === 'Paid' ? 'selected' : '' ?>>Paid</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="Pending" <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Approved" <?= ($_GET['status'] ?? '') === 'Approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="Rejected" <?= ($_GET['status'] ?? '') === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary">
                        Filter
                    </button>

                    <a href="/employee-leaves" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Rejection Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employeeLeaves)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No leave requests yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($employeeLeaves as $index => $leave): ?>
                            <tr>
                                <td><?= (int) (($pagination['page'] - 1) * $pagination['limit'] + $index + 1) ?></td>
                                <td><strong><?= htmlspecialchars($employee['employee_name'] ?? 'N/A') ?></strong></td>
                                <td><?= htmlspecialchars(['S' => 'Sick', 'C' => 'Casual', 'P' => 'Paid'][$leave['leave_type']] ?? ($leave['leave_type'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($leave['start_date'] ?? '') ?></td>
                                <td><?= htmlspecialchars($leave['end_date'] ?? '') ?></td>
                                <td><?= htmlspecialchars($leave['reason'] ?? '') ?></td>
                                <td>
                                    <?php
                                    $status = $leave['status'] ?? 'Pending';
                                    $badgeClass = $status === 'Approved' ? 'bg-success' : ($status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark');
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                </td>
                                <td>
                                    <?php if ($status === 'Rejected' && !empty($leave['rejection_reason'])): ?>
                                        <?= htmlspecialchars($leave['rejection_reason']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
            $query = [
                'leave_type' => $_GET['leave_type'] ?? '',
                'status' => $_GET['status'] ?? ''
            ];
        ?>
        <?php if (($pagination['totalPages'] ?? 1) > 1): ?>
            <nav aria-label="Employee leave pagination" class="mt-3">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= ($pagination['page'] <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="/employee-leaves?<?= http_build_query(array_merge($query, ['page' => max(1, $pagination['page'] - 1)])) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                        <li class="page-item <?= ($i === $pagination['page']) ? 'active' : '' ?>">
                            <a class="page-link" href="/employee-leaves?<?= http_build_query(array_merge($query, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($pagination['page'] >= $pagination['totalPages']) ? 'disabled' : '' ?>">
                        <a class="page-link" href="/employee-leaves?<?= http_build_query(array_merge($query, ['page' => min($pagination['totalPages'], $pagination['page'] + 1)])) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
