<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Leave Applications</h2>
</div>

<form class="row g-3 mb-4" method="GET" action="/leaves">
    <div class="col-md-4">
        <input type="text" name="employee_name" class="form-control" placeholder="Search by employee" value="<?= htmlspecialchars($_GET['employee_name'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <select name="status" class="form-select">
            <option value="">All statuses</option>
            <option value="Pending" <?= (isset($_GET['status']) && $_GET['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
            <option value="Approved" <?= (isset($_GET['status']) && $_GET['status'] === 'Approved') ? 'selected' : '' ?>>Approved</option>
            <option value="Rejected" <?= (isset($_GET['status']) && $_GET['status'] === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
        </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="/leaves" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<table class="table table-striped bg-white shadow-sm">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Employee Name</th>
            <th>Leave Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Rejection Reason</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($leaves)): ?>
        <tr>
            <td colspan="9" class="text-center text-muted py-4">No data available</td>
        </tr>
        <?php else: ?>
            <?php foreach ($leaves as $leave): ?>
            <tr>
                <td><?= $leave['id'] ?></td>
                <td><strong><?= htmlspecialchars($leave['employee_name']) ?></strong></td>
                <td><?php
                    $leaveTypeLabels = ['S' => 'Sick', 'C' => 'Casual', 'P' => 'Paid'];
                    echo htmlspecialchars($leaveTypeLabels[$leave['leave_type']] ?? $leave['leave_type'] ?? '');
                ?></td>
                <td><?= $leave['start_date'] ?></td>
                <td><?= $leave['end_date'] ?></td>
                <td><?= htmlspecialchars($leave['reason']) ?></td>
                <td><?= htmlspecialchars($leave['status']) ?></td>
                <td>
                    <?php if (($leave['status'] ?? '') === 'Rejected' && !empty($leave['rejection_reason'])): ?>
                        <?= htmlspecialchars($leave['rejection_reason']) ?>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (in_array($leave['status'], ['Pending', 'Rejected'], true)): ?>
                        <a href="/leaves-approve?id=<?= $leave['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                        <?php if ($leave['status'] === 'Pending'): ?>
                            <a href="/leaves-reject?id=<?= $leave['id'] ?>" class="btn btn-sm btn-danger">Reject</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-muted">No actions</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if ($totalPages > 1): ?>
<nav aria-label="Leave pagination" class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="/leaves?page=<?= max(1, $page - 1) ?>&employee_name=<?= urlencode($_GET['employee_name'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                <a class="page-link" href="/leaves?page=<?= $i ?>&employee_name=<?= urlencode($_GET['employee_name'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link" href="/leaves?page=<?= min($totalPages, $page + 1) ?>&employee_name=<?= urlencode($_GET['employee_name'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>">Next</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>