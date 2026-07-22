<?php 
    $currentPage = (int) ($page ?? 1);
    $totalPages = (int) ($totalSummaryPages ?? 1);
?>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th class="text-center">Approved</th>
                <th class="text-center">Pending</th>
                <th class="text-center">Rejected</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($employeeLeaveSummary)): ?>
                <?php foreach ($employeeLeaveSummary as $summary): ?>
                    <tr>
                        <td><?= htmlspecialchars($summary['employee_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($summary['department_name'] ?? 'N/A') ?></td>
                        <td class="text-center text-success fw-bold"><?= (int) ($summary['approved_leaves'] ?? 0) ?></td>
                        <td class="text-center text-warning fw-bold"><?= (int) ($summary['pending_leaves'] ?? 0) ?></td>
                        <td class="text-center text-danger fw-bold"><?= (int) ($summary['rejected_leaves'] ?? 0) ?></td>
                        <td class="text-center fw-bold"><?= (int) ($summary['total_leaves'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">No leave data available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination appears when totalPages > 1 -->
<?php if ($totalPages > 1): ?>
    <nav aria-label="Employee summary pagination" class="mt-3">
        <ul class="pagination justify-content-center mb-0">
            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <a class="page-link ajax-summary-page" href="#" data-page="<?= max(1, $currentPage - 1) ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i === $currentPage) ? 'active' : '' ?>">
                    <a class="page-link ajax-summary-page" href="#" data-page="<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link ajax-summary-page" href="#" data-page="<?= min($totalPages, $currentPage + 1) ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>