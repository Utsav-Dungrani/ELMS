<?php include __DIR__ . '/../layout/header.php'; ?>

<!-- Hidden CSRF Token container for secure AJAX requests -->
<input type="hidden" id="dashboard-csrf-token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Employee Dashboard</h2>
        <p class="text-muted small mb-0">Welcome back, <?= htmlspecialchars($employee['employee_name'] ?? 'Employee') ?>.</p>
    </div>
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

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Approved</div>
                <h3 class="fw-bold mb-0 text-success"><?= (int) ($stats['approved'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Pending</div>
                <h3 class="fw-bold mb-0 text-warning"><?= (int) ($stats['pending'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Rejected</div>
                <h3 class="fw-bold mb-0 text-danger"><?= (int) ($stats['rejected'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total Requests</div>
                <h3 class="fw-bold mb-0"><?= (int) ($stats['total'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- <div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">My Leave Requests</h5>
            <a href="<?= buildUrl('employee-leaves') ?>" class="btn btn-primary btn-sm">Apply Leave</a>
        </div>
        
        <div id="dashboard-leaves-table-container">
            <?php //include __DIR__ . '/_dashboard_leaves_table.php'; ?>
        </div>
    </div>
</div> -->

<?php include __DIR__ . '/../layout/footer.php'; ?>

<!-- <script>
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.getElementById('dashboard-leaves-table-container');
    const csrfTokenElem = document.getElementById('dashboard-csrf-token');

    function fetchDashboardLeaves(page = 1) {
        const formData = new FormData();
        if (csrfTokenElem && csrfTokenElem.value) {
            formData.append('csrf_token', csrfTokenElem.value);
        }
        formData.append('page', page);

        fetch(window.location.href, {
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
        .catch(error => console.error('Error fetching dashboard leaves via AJAX:', error));
    }

    // Delegated click handler for pagination links
    tableContainer.addEventListener('click', function(e) {
        const pageLink = e.target.closest('.ajax-page-link');
        if (pageLink) {
            e.preventDefault();
            const page = pageLink.getAttribute('data-page');
            if (page) {
                fetchDashboardLeaves(page);
            }
        }
    });
});
</script> -->