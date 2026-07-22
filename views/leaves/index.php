<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Leave Applications</h2>
</div>

<!-- AJAX Filter Form -->
<form id="filter-form" class="row g-3 mb-4">
    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

    <div class="col-md-4">
        <input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="Search by employee">
    </div>
    <div class="col-md-4">
        <select name="status" id="status" class="form-select">
            <option value="">All statuses</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
        </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <button type="button" id="reset-btn" class="btn btn-outline-secondary">Reset</button>
    </div>
</form>

<?php if (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['error_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<!-- Dynamic Table Container -->
<div id="table-container">
    <?php include __DIR__ . '/_leaves_table.php'; ?>
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

        fetch('/leaves', {
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

    // Filter submit event
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchLeaves(1);
    });

    // Reset button event
    resetBtn.addEventListener('click', function() {
        filterForm.reset();
        fetchLeaves(1);
    });

    // Event delegation for dynamic pagination links
    tableContainer.addEventListener('click', function(e) {
        const pageLink = e.target.closest('.ajax-page-link');
        if (pageLink) {
            e.preventDefault();
            const page = pageLink.getAttribute('data-page');
            if (page) {
                fetchLeaves(page);
            }
        }
    });
});
</script>