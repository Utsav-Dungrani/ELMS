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
        <?php if (empty($employees)): ?>
        <tr>
            <td colspan="7" class="text-center text-muted py-4">No data available</td>
        </tr>
        <?php else: ?>
            <?php foreach ($employees as $emp): ?>
            <tr>
                <td><?= $emp['id'] ?></td>
                <td><?= htmlspecialchars($emp['employee_name']) ?></td>
                <td><?= htmlspecialchars($emp['email'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($emp['department_name'] ?? $emp['department'] ?? $emp['department_id'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($emp['joining_date'] ?? 'N/A') ?></td>
                <td>
                    <?= $emp['total_leaves'] ?? 0 ?>
                    <button class="btn btn-sm btn-info ms-1" onclick="viewSummary(<?= $emp['id'] ?>)">View Summary</button>
                </td>
                <td>
                    <form action="/employees-edit" method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-warning">Edit</button>
                    </form>
                    <form action="/employees-delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if ($totalPages > 1): ?>
<nav aria-label="Employee pagination" class="mt-3">
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