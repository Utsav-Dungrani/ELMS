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
                <td><?= htmlspecialchars($leave['start_date']) ?></td>
                <td><?= htmlspecialchars($leave['end_date']) ?></td>
                <td><?= htmlspecialchars($leave['reason']) ?></td>
                <td>
                    <?php
                    $status = $leave['status'] ?? 'Pending';
                    $badgeClass = $status === 'Approved' ? 'bg-success' : ($status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark');
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                </td>
                <td>
                    <?php if (($leave['status'] ?? '') === 'Rejected' && !empty($leave['rejection_reason'])): ?>
                        <?= htmlspecialchars($leave['rejection_reason']) ?>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (in_array($leave['status'], ['Pending', 'Rejected'], true)): ?>
                        <form action="/leaves-approve" method="POST" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="id" value="<?= $leave['id'] ?>">
                            <button class="btn btn-success btn-sm">
                                Approve
                            </button>
                        </form>
                    <?php endif; ?>
                    <?php if (in_array($leave['status'], ['Pending', 'Approved'], true)): ?>
                        <a href="/leaves-reject?id=<?= $leave['id'] ?>" class="btn btn-danger btn-sm">
                            Reject
                        </a>
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
            <a class="page-link ajax-page-link" href="#" data-page="<?= max(1, $page - 1) ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                <a class="page-link ajax-page-link" href="#" data-page="<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link ajax-page-link" href="#" data-page="<?= min($totalPages, $page + 1) ?>">Next</a>
        </li>
    </ul>
</nav>
<?php endif; ?>