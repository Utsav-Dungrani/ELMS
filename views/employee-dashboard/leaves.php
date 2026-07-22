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
        <!-- Filter Form -->
        <form id="filter-form" class="row align-items-end mb-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="col-md-3">
                <label class="form-label">Leave Type</label>
                <select name="leave_type" id="leave_type" class="form-select">
                    <option value="">All</option>
                    <option value="Sick">Sick</option>
                    <option value="Casual">Casual</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
                <button type="button" id="reset-btn" class="btn btn-outline-secondary">Reset</button>
            </div>
        </form>

        <!-- Wrapper for AJAX dynamic updates -->
        <div id="table-container">
            <?php include __DIR__ . '/_leaves_table.php'; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const tableContainer = document.getElementById('table-container');
    const resetBtn = document.getElementById('reset-btn');

    function fetchLeaves(page = 1) {
        const formData = new FormData(filterForm);
        formData.append('page', page);

        fetch('/employee-leaves', {
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
        .catch(error => console.error('Error fetching leaves:', error));
    }

    // Filter Form Submit
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchLeaves(1);
    });

    // Reset Button Click
    resetBtn.addEventListener('click', function() {
        filterForm.reset();
        fetchLeaves(1);
    });

    // Dynamic Pagination Click Delegation
    tableContainer.addEventListener('click', function(e) {
        const target = e.target.closest('.ajax-page-link');
        if (target) {
            e.preventDefault();
            const page = target.getAttribute('data-page');
            if (page) {
                fetchLeaves(page);
            }
        }
    });
});
</script>