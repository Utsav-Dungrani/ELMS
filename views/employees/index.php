<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Employees List</h2>
    <a href="/employees-create" class="btn btn-primary">Add Employee</a>
</div>

<form class="row g-3 mb-4" method="GET" action="/employees">
    <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="Search by name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <select name="department" class="form-select">
            <option value="">All Departments</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?= htmlspecialchars($department['department_name']) ?>" <?= (($_GET['department'] ?? '') === $department['department_name']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($department['department_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-secondary">Search</button>
        <a href="/employees" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<?php if (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"> <?= htmlspecialchars($_SESSION['error_message']) ?> </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

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
                <td><?= $emp['total_leaves'] ?? 0 ?>
                    <button class="btn btn-sm btn-info" onclick="viewSummary(<?= $emp['id'] ?>)">View Summary</button>
                </td>
                <td>
                    <a href="/employees-edit?id=<?= $emp['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="/employees-delete?id=<?= $emp['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
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
            <a class="page-link" href="/employees?page=<?= max(1, $page - 1) ?>&name=<?= urlencode($_GET['name'] ?? '') ?>&department=<?= urlencode($_GET['department'] ?? '') ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                <a class="page-link" href="/employees?page=<?= $i ?>&name=<?= urlencode($_GET['name'] ?? '') ?>&department=<?= urlencode($_GET['department'] ?? '') ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link" href="/employees?page=<?= min($totalPages, $page + 1) ?>&name=<?= urlencode($_GET['name'] ?? '') ?>&department=<?= urlencode($_GET['department'] ?? '') ?>">Next</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<div class="modal fade" id="summaryModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Employee Summary</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="summaryBody">
                Loading...
            </div>
        </div>
    </div>
</div>

<script>
    function viewSummary(id){fetch('/employee-summary?id='+id)
    .then(r=>r.json())
        .then(data=>{
            let html=`
                <h4>${data.employee_name}</h4>
                    <p>
                        <b>Email:</b> ${data.email}<br>
                        <b>Department:</b> ${data.department_name}<br>
                        <b>Joining:</b> ${data.joining_date}
                    </p>
                <hr>
                <div class="row">
                    <div class="col">
                        <span class="badge bg-success">Approved : ${data.approved}</span>
                    </div>
                    <div class="col">
                        <span class="badge bg-warning text-dark">Pending : ${data.pending}</span>
                    </div>
                    <div class="col">
                        <span class="badge bg-danger">Rejected : ${data.rejected}</span>
                    </div>
                    <div class="col">
                        <span class="badge bg-primary">Total : ${data.total}</span>
                    </div>
                </div>
                <hr>
                <table class="table table-bordered">
                    <tr>
                        <th>Type</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Reject Reason</th>
                    </tr>`;

            if (data.history.length === 0) {
                html += `
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No leave records found.
                        </td>
                    </tr>`;
            } else {
                data.history.forEach(function(l){
                    html += `
                        <tr>
                            <td>${l.leave_type}</td>
                            <td>${l.start_date}</td>
                            <td>${l.end_date}</td>
                            <td>${l.status}</td>
                            <td>${l.reason}</td>
                            <td>
                                ${l.status === 'Rejected' ? (l.rejection_reason || '-') : '-'}
                            </td>
                        </tr>`;
                });
            }

        html+=`</table>`;
        
        document.getElementById('summaryBody').innerHTML=html;
        
        new bootstrap.Modal(document.getElementById('summaryModal')).show();

    });
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>