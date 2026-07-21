<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Dashboard</h2>
        <p class="text-muted small mb-0">Overview of staff metrics and leave statistics.</p>
    </div>
    <div>
        <a href="index.php?route=leaves-create" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Request Leave
        </a>
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

<!-- Quick Navigation Links -->
<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-1">Manage Employees</h6>
                    <p class="text-muted small mb-0">Add new staff or manage existing profiles.</p>
                </div>
                <a href="index.php?route=employees" class="btn btn-outline-primary btn-sm">Go to Employees</a>
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
                <a href="index.php?route=leaves" class="btn btn-outline-primary btn-sm">Go to Leaves</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>