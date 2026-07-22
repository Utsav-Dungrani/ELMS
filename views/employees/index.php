<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Employees List</h2>
    <a href="/employees-create" class="btn btn-primary">Add Employee</a>
</div>

<!-- AJAX Filter Form -->
<form id="filter-form" class="row g-3 mb-4">
    <!-- CSRF Token Field -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

    <div class="col-md-4">
        <input type="text" name="name" id="search-name" class="form-control" placeholder="Search by name">
    </div>
    <div class="col-md-4">
        <select name="department" id="search-department" class="form-select">
            <option value="">All Departments</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?= htmlspecialchars($department['department_name']) ?>">
                    <?= htmlspecialchars($department['department_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-secondary">Search</button>
        <button type="button" id="reset-btn" class="btn btn-outline-secondary">Reset</button>
    </div>
</form>

<?php if (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"> <?= htmlspecialchars($_SESSION['error_message']) ?> </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<!-- Dynamic Container for Table & Pagination -->
<div id="table-container">
    <?php include __DIR__ . '/_employees_table.php'; ?>
</div>

<!-- Modal -->
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

<?php include __DIR__ . '/../layout/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const tableContainer = document.getElementById('table-container');
    const resetBtn = document.getElementById('reset-btn');

    function fetchEmployees(page = 1) {
        const formData = new FormData(filterForm);
        formData.append('page', page);

        fetch('/employees', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
        })
        .catch(error => console.error('Error fetching employees:', error));
    }

    // Submit Filter
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchEmployees(1);
    });

    // Reset Form
    resetBtn.addEventListener('click', function() {
        filterForm.reset();
        fetchEmployees(1);
    });

    // Dynamic Pagination Handling
    tableContainer.addEventListener('click', function(e) {
        const pageLink = e.target.closest('.ajax-page-link');
        if (pageLink) {
            e.preventDefault();
            const page = pageLink.getAttribute('data-page');
            if (page) {
                fetchEmployees(page);
            }
        }
    });
});

function viewSummary(id) {
    fetch('/employee-summary?id=' + id)
    .then(r => r.json())
    .then(data => {
        let html = `
            <h4>${data.employee_name}</h4>
            <p>
                <b>Email:</b> ${data.email}<br>
                <b>Department:</b> ${data.department_name}<br>
                <b>Joining:</b> ${data.joining_date}
            </p>
            <hr>
            <div class="row">
                <div class="col"><span class="badge bg-success">Approved : ${data.approved}</span></div>
                <div class="col"><span class="badge bg-warning text-dark">Pending : ${data.pending}</span></div>
                <div class="col"><span class="badge bg-danger">Rejected : ${data.rejected}</span></div>
                <div class="col"><span class="badge bg-primary">Total : ${data.total}</span></div>
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
            html += `<tr><td colspan="6" class="text-center text-muted">No leave records found.</td></tr>`;
        } else {
            data.history.forEach(function(l) {
                html += `
                    <tr>
                        <td>${l.leave_type}</td>
                        <td>${l.start_date}</td>
                        <td>${l.end_date}</td>
                        <td>${l.status}</td>
                        <td>${l.reason}</td>
                        <td>${l.status === 'Rejected' ? (l.rejection_reason || '-') : '-'}</td>
                    </tr>`;
            });
        }

        html += `</table>`;
        document.getElementById('summaryBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('summaryModal')).show();
    });
}
</script>