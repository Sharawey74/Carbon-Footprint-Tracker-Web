<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <?php if (isset($extraCss)): ?>
        <?= $extraCss ?>
    <?php endif; ?>
    <style>
        /* Glowing effect for brand text */
        .brand-text {
            margin-left: 10px;
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            text-shadow: 0 0 5px rgba(46, 204, 113, 0.5), 
                         0 0 10px rgba(46, 204, 113, 0.3), 
                         0 0 15px rgba(46, 204, 113, 0.2);
            animation: glow 2s ease-in-out infinite alternate;
        }
        
        @keyframes glow {
            from {
                text-shadow: 0 0 5px rgba(46, 204, 113, 0.5), 
                             0 0 10px rgba(46, 204, 113, 0.3), 
                             0 0 15px rgba(46, 204, 113, 0.2);
            }
            to {
                text-shadow: 0 0 10px rgba(46, 204, 113, 0.7), 
                             0 0 20px rgba(46, 204, 113, 0.5), 
                             0 0 30px rgba(46, 204, 113, 0.3);
            }
        }
    </style>
</head>
<body>
    <header class="navbar navbar-dark bg-dark navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= APP_URL ?>/">
                <img src="<?= APP_URL ?>/img/logo.png" alt="" height="40">
                <span class="brand-text glow-effect"><i class="fas fa-leaf text-success me-2"></i>Carbon Footprint Tracker</span>
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php if ($_SESSION['user_role'] === 'BranchUser'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/?controller=branch&action=dashboard">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/?controller=branch&action=dataEntry">Data Entry</a>
                            </li>
                        <?php elseif ($_SESSION['user_role'] === 'OPManager'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/?controller=op_manager&action=dashboard">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/?controller=op_manager&action=reports">Reports</a>
                            </li>
                        <?php elseif ($_SESSION['user_role'] === 'CIO'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/?controller=cio&action=dashboard">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/?controller=cio&action=analytics">Analytics</a>
                            </li>
                        <?php elseif ($_SESSION['user_role'] === 'CEO'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/?controller=ceo&action=dashboard">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/?controller=ceo&action=reports">Reports</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/?controller=auth&action=profile">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/?controller=auth&action=logout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="container my-4">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
                <?= $_SESSION['flash_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php endif; ?>
    </main>

    <footer class="bg-light py-3 mt-auto">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= APP_URL ?>/js/main.js"></script>
    <script src="<?= APP_URL ?>/js/contentAPIs.js"></script>
    <?php if (isset($extraJs)): ?>
        <?= $extraJs ?>
    <?php endif; ?>
</body>
</html>
