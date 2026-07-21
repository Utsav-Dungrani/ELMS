<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELMS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: #1e293b;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff !important;
        }

        .nav-link {
            font-weight: 500;
            color: #cbd5e1 !important;
            transition: all 0.2s ease-in-out;
            border-radius: 6px;
            padding: 8px 16px !important;
        }

        .nav-link:hover, .nav-link.active {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .btn-logout {
            border-color: #cbd5e1;
            color: #cbd5e1;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .btn-logout:hover {
            background-color: #ef4444;
            border-color: #ef4444;
            color: #fff;
        }

        .main-content {
            flex: 1 0 auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php?route=dashboard">
                <i class="fa-solid fa-briefcase text-primary fs-4"></i>
                <span>ELMS</span>
            </a>
            <div class="collapse navbar-collapse" id="navbarMain">
                <?php $current_route = $_GET['route'] ?? ''; ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_route === 'dashboard' || $current_route === '') ? 'active' : ''; ?>" href="index.php?route=dashboard">
                            <i class="fa-solid fa-chart-line me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_route === 'employees' ? 'active' : ''; ?>" href="index.php?route=employees">
                            <i class="fa-solid fa-users me-2"></i>Employees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_route === 'leaves' ? 'active' : ''; ?>" href="index.php?route=leaves">
                            <i class="fa-solid fa-calendar-check me-2"></i>Leaves
                        </a>
                    </li>
                </ul>

                <!-- Right Action / Logout -->
                <div class="d-flex align-items-center gap-3">
                    <a href="index.php?route=logout" class="btn btn-outline-light btn-sm btn-logout px-3 py-2 rounded-2">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <main class="main-content my-4">
        <div class="container">