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
                    <td colspan="8" class="text-center text-muted py-4">No leave requests found.</td>
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

<?php if (($pagination['totalPages'] ?? 1) > 1): ?>
    <nav aria-label="Employee leave pagination" class="mt-3">
        <ul class="pagination justify-content-center mb-0">
            <li class="page-item <?= ($pagination['page'] <= 1) ? 'disabled' : '' ?>">
                <a class="page-link ajax-page-link" href="#" data-page="<?= max(1, $pagination['page'] - 1) ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                <li class="page-item <?= ($i === $pagination['page']) ? 'active' : '' ?>">
                    <a class="page-link ajax-page-link" href="#" data-page="<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($pagination['page'] >= $pagination['totalPages']) ? 'disabled' : '' ?>">
                <a class="page-link ajax-page-link" href="#" data-page="<?= min($pagination['totalPages'], $pagination['page'] + 1) ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>