<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Dashboard</h2>
        <p class="text-muted small mb-0">Overview of staff metrics and leave statistics.</p>
    </div>
</div>

<!-- Key Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Total Employees -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 rounded-3">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted fw-medium small d-block mb-1">Total Employees</span>
                    <h3 class="fw-bold mb-0 text-dark"><?php echo $stats['total_employees']; ?></h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 fs-3">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Leave Requests -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 rounded-3">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted fw-medium small d-block mb-1">Total Requests</span>
                    <h3 class="fw-bold mb-0 text-dark"><?php echo $stats['total_leaves']; ?></h3>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 fs-3">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Approved Leaves -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 rounded-3">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted fw-medium small d-block mb-1">Approved Leaves</span>
                    <h3 class="fw-bold mb-0 text-success"><?php echo $stats['approved_leaves']; ?></h3>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 fs-3">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Leaves -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 rounded-3">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted fw-medium small d-block mb-1">Pending Requests</span>
                    <h3 class="fw-bold mb-0 text-warning"><?php echo $stats['pending_leaves']; ?></h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 fs-3">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Leave Summary -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="fw-bold mb-1">Employee Leave Summary</h5>
                <p class="text-muted small mb-0">Approved, pending, rejected, and total leave requests per employee.</p>
            </div>
        </div>
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
                                <td><?= htmlspecialchars($summary['employee_name']) ?></td>
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
        <?php if ($totalSummaryPages > 1): ?>
            <nav aria-label="Employee summary pagination" class="mt-3">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="/dashboard?page=<?= max(1, $page - 1) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalSummaryPages; $i++): ?>
                        <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                            <a class="page-link" href="/dashboard?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $totalSummaryPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="/dashboard?page=<?= min($totalSummaryPages, $page + 1) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Navigation Links -->
<div class="row g-3 mt-1">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-1">Manage Employees</h6>
                    <p class="text-muted small mb-0">Add new staff or manage existing profiles.</p>
                </div>
                <a href="/employees" class="btn btn-outline-primary btn-sm">Go to Employees</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-1">Review Leave Requests</h6>
                    <p class="text-muted small mb-0">Approve or reject pending leave applications.</p>
                </div>
                <a href="/leaves" class="btn btn-outline-primary btn-sm">Go to Leaves</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>