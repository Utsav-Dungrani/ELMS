<?php
include 'header.php'; 
?>

<div class="row justify-content-center align-items-center py-5">
    <div class="col-lg-6 col-md-8 text-center">
        <!-- Animated / Illustrated Graphic Element -->
        <div class="error-graphic-container mb-4">
            <span class="error-code display-1 fw-bold text-primary">4</span>
            <div class="compass-icon-wrapper d-inline-block position-relative mx-2">
                <i class="fa-solid fa-compass fa-spin-pulse text-warning display-1" style="--fa-animation-duration: 12s;"></i>
            </div>
            <span class="error-code display-1 fw-bold text-primary">4</span>
        </div>

        <!-- Headline & Text -->
        <h1 class="h2 fw-bold text-dark mb-3">Oops! Page Lost in Workspace</h1>
        <p class="text-muted mb-4 lead fs-6">
            The page or leave record you are looking for might have been moved, renamed, or no longer exists in the system.
        </p>

        <!-- Quick Action Buttons -->
        <div class="d-flex justify-content-center gap-3 flex-wrap mb-4">
            <a href="/employees" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm rounded-3">
                <i class="fa-solid fa-house me-2"></i>Back to Employees
            </a>
            <a href="/leaves" class="btn btn-outline-secondary px-4 py-2 fw-semibold rounded-3">
                <i class="fa-solid fa-calendar-check me-2"></i>View Leaves
            </a>
        </div>

        <!-- Optional Helpful Search or Support Callout -->
        <div class="card border-0 bg-white shadow-sm p-3 rounded-4 mt-4">
            <div class="d-flex align-items-center justify-content-center gap-2 text-muted small">
                <i class="fa-solid fa-circle-info text-info"></i>
                <span>Think this is an error? Please contact your system HR administrator.</span>
            </div>
        </div>
    </div>
</div>

<style>
    .error-code {
        font-size: 6rem;
        letter-spacing: -2px;
        line-height: 1;
        background: linear-gradient(135deg, #2563eb, #1e293b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .compass-icon-wrapper {
        font-size: 5rem;
        vertical-align: middle;
    }

    @media (max-width: 576px) {
        .error-code {
            font-size: 4.5rem;
        }
        .compass-icon-wrapper {
            font-size: 3.5rem;
        }
    }
</style>

<?php
include 'footer.php'; 
?>