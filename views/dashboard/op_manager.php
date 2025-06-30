<!DOCTYPE html>
<html>
<head>
    <title>OP Manager Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary-color: #2e7d32;
            --primary-light: #60ad5e;
            --primary-dark: #005005;
            --secondary-color: #2196F3;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --success-color: #4CAF50;
            --info-color: #03A9F4;
            --dark-text: #333;
            --light-text: #fff;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            box-sizing: border-box;
            font-family: 'Poppins', Arial, sans-serif;
            transition: var(--transition);
        }
        
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: var(--dark-text);
            line-height: 1.6;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            position: relative;
            z-index: 10;
        }
        
        .header-logo {
            display: flex;
            align-items: center;
        }
        
        .header-title {
            margin-left: 15px;
        }
        
        .header-title h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            position: relative;
            display: inline-block;
        }
        
        .header-title h1:after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: rgba(255,255,255,0.7);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
        }
        
        .header-title h1:hover:after {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .header-title h1 i {
            color: #8bc34a;
            text-shadow: 0 0 10px rgba(139, 195, 74, 0.5);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { text-shadow: 0 0 10px rgba(139, 195, 74, 0.5); }
            50% { text-shadow: 0 0 20px rgba(139, 195, 74, 0.8); }
            100% { text-shadow: 0 0 10px rgba(139, 195, 74, 0.5); }
        }
        
        .header-title h2 {
            margin: 5px 0 0;
            font-size: 14px;
            font-weight: 400;
            opacity: 0.9;
        }
        
        .user-info {
            text-align: right;
            margin-right: 15px;
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: var(--border-radius);
            backdrop-filter: blur(5px);
        }
        
        .user-info p {
            margin: 0;
            font-size: 14px;
        }
        
        .logout-btn {
            background: linear-gradient(to right, #f44336, #e53935);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logout-btn i {
            margin-right: 8px;
        }
        
        .logout-btn:hover {
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transform: translateY(-2px);
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }
        
        .tabs {
            display: flex;
            background-color: #fff;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            border-bottom: none;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        
        .tabs button {
            background-color: #fff;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 15px 20px;
            font-size: 14px;
            font-weight: 500;
            flex-grow: 1;
            border-right: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            color: #555;
        }
        
        .tabs button:last-child {
            border-right: none;
        }
        
        .tabs button i {
            margin-right: 8px;
            font-size: 16px;
        }
        
        .tabs button:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .tabs button:hover {
            background-color: #f9f9f9;
            color: var(--primary-color);
        }
        
        .tabs button.active {
            background-color: #fff;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .tabs button.active:after {
            transform: scaleX(1);
        }
        
        .tab-content {
            display: none;
            background-color: white;
            padding: 25px;
            border: 1px solid #e0e0e0;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            animation: fadeIn 0.5s ease;
            min-height: 300px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .tab-content.active {
            display: block;
        }
        
        .alert {
            padding: 15px;
            margin: 10px 0;
            border-radius: var(--border-radius);
            position: relative;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: opacity 0.5s ease-in-out;
            border-left: 4px solid;
            animation: slideInDown 0.5s forwards;
        }
        
        @keyframes slideInDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .alert-error, .alert-danger {
            background: #ffeaea;
            border-color: var(--danger-color);
            color: #d32f2f;
        }
        
        .alert-success {
            background: #e8f5e9;
            border-color: var(--success-color);
            color: #2e7d32;
        }
        
        .alert-info {
            background: #e3f2fd;
            border-color: var(--secondary-color);
            color: #1976d2;
        }
        
        .alert-warning {
            background: #fff8e1;
            border-color: var(--warning-color);
            color: #f57c00;
        }
        
        .alert .close-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            font-size: 16px;
            color: #666;
            transition: color 0.3s ease;
        }
        
        .alert .close-btn:hover {
            color: #333;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            grid-gap: 12px;
            margin-bottom: 15px;
        }
        
        .form-row {
            display: flex;
            flex-direction: column;
        }
        
        .form-row label {
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: #555;
        }
        
        input, select, button, textarea {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            width: 100%;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
            outline: none;
        }
        
        button {
            cursor: pointer;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            padding: 12px 20px;
            font-weight: 500;
            margin-right: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        button i {
            margin-right: 8px;
        }
        
        button:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transform: translateY(-2px);
        }
        
        button.delete-btn {
            background: linear-gradient(135deg, #f44336, #e53935);
        }
        
        button.reset-btn {
            background: linear-gradient(135deg, #ff9800, #fb8c00);
        }
        
        button.refresh-btn {
            background: linear-gradient(135deg, #2196F3, #1e88e5);
        }
        
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .action-buttons button {
            padding: 8px 12px;
            font-size: 13px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        table, th, td {
            border: none;
        }
        
        th, td {
            text-align: left;
            padding: 15px;
        }
        
        th {
            background: linear-gradient(to right, #f5f5f5, #e0e0e0);
            color: #555;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        tr {
            border-bottom: 1px solid #eee;
            transition: all 0.3s ease;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f5f5f5;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .footer {
            background: linear-gradient(135deg, #333, #222);
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
        
        /* Add some styling for selected rows */
        .selected-row {
            background-color: #e3f2fd !important;
            border-left: 4px solid #1890ff;
            box-shadow: 0 2px 8px rgba(24, 144, 255, 0.2);
        }
        
        /* Make rows look clickable */
        .user-row, .branch-row, .production-row, .packaging-row, .distribution-row {
            cursor: pointer;
        }
        
        /* Style for the clear button */
        .btn-secondary {
            background: linear-gradient(135deg, #757575, #616161);
            color: white;
        }
        
        /* Links in tables */
        .download-link, .view-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .download-link {
            background: linear-gradient(135deg, var(--success-color), #43a047);
            color: white;
            box-shadow: 0 2px 5px rgba(76, 175, 80, 0.3);
        }
        
        .download-link:hover {
            background: linear-gradient(135deg, #43a047, #388e3c);
            color: white;
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.4);
            transform: translateY(-2px);
        }
        
        .view-link {
            background: linear-gradient(135deg, var(--secondary-color), #1e88e5);
            color: white;
            box-shadow: 0 2px 5px rgba(33, 150, 243, 0.3);
        }
        
        .view-link:hover {
            background: linear-gradient(135deg, #1e88e5, #1976d2);
            color: white;
            box-shadow: 0 4px 8px rgba(33, 150, 243, 0.4);
            transform: translateY(-2px);
        }
        
        .download-link i, .view-link i {
            margin-right: 8px;
        }
        
        /* Dashboard summary styling */
        .dashboard-summary {
            margin-bottom: 30px;
            background: linear-gradient(to right, #ffffff, #f9f9f9);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #eee;
        }
        
        .dashboard-summary h3 {
            color: #333;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            position: relative;
        }
        
        .dashboard-summary h3:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background: var(--primary-color);
        }
        
        .metrics-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        
        .metric-card {
            flex: 1;
            min-width: 200px;
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .metric-card.total {
            border-left: 4px solid var(--danger-color);
            background: linear-gradient(to right, #fff, #fafafa);
        }
        
        .metric-icon {
            font-size: 28px;
            height: 60px;
            width: 60px;
            line-height: 60px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, rgba(76,175,80,0.1), rgba(76,175,80,0.2));
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .metric-card.total .metric-icon {
            background: linear-gradient(135deg, rgba(244,67,54,0.1), rgba(244,67,54,0.2));
            color: var(--danger-color);
        }
        
        .metric-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #333;
        }
        
        .metric-label {
            color: #666;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .chart-container {
            margin-top: 30px;
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #eee;
        }
        
        .chart-container h3 {
            color: #333;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            position: relative;
        }
        
        .chart-container h3:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background: var(--secondary-color);
        }
        
        .action-buttons-container {
            display: flex;
            gap: 10px;
        }
        
        @media (max-width: 768px) {
            .metrics-container {
                flex-direction: column;
            }
            .metric-card {
                min-width: 100%;
            }
            .action-buttons {
                flex-direction: column;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .tabs {
                flex-wrap: wrap;
            }
            .tabs button {
                flex-basis: 50%;
            }
        }
        
        /* Add pulse animation for new data */
        @keyframes pulse-highlight {
            0% { background-color: rgba(76, 175, 80, 0.2); }
            50% { background-color: rgba(76, 175, 80, 0.4); }
            100% { background-color: rgba(0, 0, 0, 0); }
        }
        
        .new-data {
            animation: pulse-highlight 2s ease;
        }
        
        /* Badge styling for emissions */
        .emissions-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 12px;
            background-color: #e0e0e0;
            color: #333;
        }
        
        .emissions-high {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .emissions-medium {
            background-color: #fff8e1;
            color: #ff8f00;
        }
        
        .emissions-low {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        /* Toast notification system */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .toast {
            margin-bottom: 10px;
            padding: 15px 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            background: white;
            border-left: 4px solid;
            animation: toast-in 0.5s ease forwards;
            display: flex;
            align-items: center;
        }
        
        @keyframes toast-in {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .toast-success {
            border-color: var(--success-color);
        }
        
        .toast-error {
            border-color: var(--danger-color);
        }
        
        .toast-info {
            border-color: var(--info-color);
        }
        
        .toast-warning {
            border-color: var(--warning-color);
        }
        
        .toast-icon {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .toast-success .toast-icon {
            color: var(--success-color);
        }
        
        .toast-error .toast-icon {
            color: var(--danger-color);
        }
        
        .toast-info .toast-icon {
            color: var(--info-color);
        }
        
        .toast-warning .toast-icon {
            color: var(--warning-color);
        }
        
        /* Add styles for the side-by-side layout */
        .content-layout {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        .form-section {
            flex: 0 0 30%;
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            align-self: flex-start;
        }
        
        .table-section {
            flex: 0 0 68%;
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .content-layout {
                flex-direction: column;
            }
            
            .form-section, .table-section {
                flex: 0 0 100%;
                width: 100%;
            }
        }
        
        /* Update each tab content to use the new layout */
        
        /* Users Tab */
        #users.tab-content .content-layout {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        /* Branches Tab */
        #branches.tab-content .content-layout {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        /* Production Tab */
        #production.tab-content .content-layout {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        /* Packaging Tab */
        #packaging.tab-content .content-layout {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        /* Distribution Tab */
        #distribution.tab-content .content-layout {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        /* Report Tab does not need the layout change as it doesn't have forms */
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="header-logo">
            <img src="<?= APP_URL ?>/img/coffee_eco_logo.svg" alt="Logo" width="50" height="50" class="animate__animated animate__fadeIn">
            <div class="header-title">
                <h1><i class="fas fa-leaf"></i> Coffee Factory Carbon Tracker</h1>
                <h2>Operational Manager Dashboard</h2>
            </div>
        </div>
        <div class="user-info animate__animated animate__fadeIn">
            <p><i class="fas fa-user-circle"></i> <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User' ?></p>
            <?php if (isset($_SESSION['branch_id'])): ?>
                <p><i class="fas fa-building"></i> Branch ID: <?= htmlspecialchars($_SESSION['branch_id']) ?></p>
            <?php endif; ?>
        </div>
        <a href="<?= APP_URL ?>/?controller=auth&action=logout" class="animate__animated animate__fadeIn">
            <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log Out</button>
        </a>
    </div>

    <div class="container">
        <!-- Toast Container for Notifications -->
        <div class="toast-container" id="toastContainer"></div>
        
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?>">
                <i class="fas fa-info-circle"></i> <?= $_SESSION['flash_message'] ?>
                <span class="close-btn">&times;</span>
            </div>
            <?php unset($_SESSION['flash_message']); unset($_SESSION['flash_type']); ?>
        <?php endif; ?>
        
    <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
                <span class="close-btn">&times;</span>
            </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                <span class="close-btn">&times;</span>
            </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
        <div class="tabs animate__animated animate__fadeIn">
            <button <?= ($data['active_tab'] == 'users') ? 'class="active"' : '' ?> onclick="showTab('users')"><i class="fas fa-users"></i> Users</button>
            <button <?= ($data['active_tab'] == 'branches') ? 'class="active"' : '' ?> onclick="showTab('branches')"><i class="fas fa-building"></i> Branches</button>
            <button <?= ($data['active_tab'] == 'production') ? 'class="active"' : '' ?> onclick="showTab('production')"><i class="fas fa-industry"></i> Production</button>
            <button <?= ($data['active_tab'] == 'packaging') ? 'class="active"' : '' ?> onclick="showTab('packaging')"><i class="fas fa-box"></i> Packaging</button>
            <button <?= ($data['active_tab'] == 'distribution') ? 'class="active"' : '' ?> onclick="showTab('distribution')"><i class="fas fa-truck"></i> Distribution</button>
            <button <?= ($data['active_tab'] == 'report') ? 'class="active"' : '' ?> onclick="showTab('report')"><i class="fas fa-chart-bar"></i> Report</button>
    </div>

    <!-- Users Tab -->
    <div id="users" class="tab-content <?= ($data['active_tab'] == 'users') ? 'active' : '' ?>">
        <h2>User Management</h2>
        <form id="userForm" method="POST">
            <input type="hidden" name="tab" value="users">
            <input type="hidden" id="editUserId" name="user_id" value="">
            <div class="content-layout">
                <div class="form-section">
            <div class="form-grid">
                <div class="form-row">
                    <label for="userName">User Name:</label>
                    <input type="text" id="userName" name="name" required>
                </div>
                <div class="form-row">
                    <label for="userRole">Role:</label>
                    <select id="userRole" name="role" required>
                    <option value="BranchUser">Branch User</option>
                    <option value="OPManager">OP Manager</option>
                        <option value="CIO">CIO</option>
                        <option value="CEO">CEO</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="userEmail">Email:</label>
                    <input type="email" id="userEmail" name="email" required>
                </div>
                <div class="form-row">
                    <label for="userPassword">Password:</label>
                    <input type="password" id="userPassword" name="password" required>
                </div>
                <div class="form-row">
                    <label for="userBranch">Branch:</label>
                    <select id="userBranch" name="branch_id">
                        <option value="">Select Branch</option>
                        <?php foreach ($data['branches'] ?? [] as $branch): ?>
                            <option value="<?= $branch->getBranchId() ?>"><?= htmlspecialchars($branch->getLocation()) ?></option>
                        <?php endforeach; ?>
                </select>
                </div>
                <div class="form-row">
                    <label for="forcePasswordChange">Force Password Change:</label>
                    <input type="checkbox" id="forcePasswordChange" name="force_password_change" style="width: auto;">
                </div>
            </div>
            
                    <!-- Moved action buttons here below the form data -->
            <div class="action-buttons">
                <button type="submit" name="action" value="add_user" id="addUserBtn">Add</button>
                <button type="submit" name="action" value="delete_user" class="delete-btn" id="deleteUserBtn">Delete</button>
                <button type="submit" name="action" value="save_user" id="saveUserBtn">Save</button>
                <button type="submit" name="action" value="reset_password" class="reset-btn" id="resetPasswordBtn">Reset Password</button>
                <button type="submit" name="action" value="refresh_user" class="refresh-btn">Refresh</button>
                <button type="button" id="clearUserForm" class="btn-secondary">Clear Form</button>
            </div>
                </div>
                <div class="table-section">
        <table id="usersTable">
            <tr>
                    <th>User ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                    <th>Branch ID</th>
                    <th>Force Password Change</th>
                <th>Actions</th>
            </tr>
                <?php foreach ($data['users'] ?? [] as $user):
                    // Ensure $user is an object
                    if (!is_object($user)) {
                        // Potentially log an error or skip this iteration
                        continue;
                    }
                ?>
            <tr class="user-row" data-user-id="<?= $user->userID ?>" data-user-name="<?= htmlspecialchars($user->userName) ?>" 
                data-user-role="<?= htmlspecialchars($user->userRole) ?>" data-user-email="<?= htmlspecialchars($user->userEmail) ?>" 
                data-branch-id="<?= $user->branchID ?? '' ?>" data-force-password-change="<?= $user->forcePasswordChange ? '1' : '0' ?>">
                    <td><?= htmlspecialchars($user->userID) ?></td>
                <td><?= htmlspecialchars($user->userName) ?></td>
                <td><?= htmlspecialchars($user->userRole) ?></td>
                <td><?= htmlspecialchars($user->userEmail) ?></td>
                    <td><?= htmlspecialchars($user->branchID ?? 'N/A') ?></td>
                    <td><?= $user->forcePasswordChange ? 'Yes' : 'No' ?></td>
                <td>
                        <form method="POST" style="display: inline;">
                                    <input type="hidden" name="tab" value="users">
                        <input type="hidden" name="user_id" value="<?= $user->userID ?>">
                            <button type="submit" name="action" value="delete_user" class="delete-btn">Delete</button>
                            <button type="submit" name="action" value="reset_password" class="reset-btn">Reset Password</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
                </div>
            </div>
        </form>
    </div>

    <!-- Branches Tab -->
                    <div id="branches" class="tab-content <?= ($data['active_tab'] == 'branches') ? 'active' : '' ?>">
            <h2>Branch Management</h2>
        <form id="branchForm" method="POST">
                <input type="hidden" name="tab" value="branches">
            <input type="hidden" id="editBranchId" name="branch_id" value="">
                <div class="content-layout">
                    <div class="form-section">
            <div class="form-grid">
                <div class="form-row">
                    <label for="cityId">City:</label>
                    <select id="cityId" name="city_id" required>
                        <option value="">Select City</option>
                        <?php foreach ($data['cities'] ?? [] as $city): ?>
                            <option value="<?= $city->getCityId() ?>"><?= htmlspecialchars($city->getCityName()) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="branchLocation">Location:</label>
                    <input type="text" id="branchLocation" name="location" required>
                </div>
                <div class="form-row">
                    <label for="numEmployees">Num Employees:</label>
                    <input type="number" id="numEmployees" name="num_employees" required>
                </div>
            </div>
            </div>
                    <div class="table-section">
        <table id="branchesTable">
                <tr>
                    <th>Branch ID</th>
                    <th>City ID</th>
                    <th>Location</th>
                    <th>Num Employees</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($data['branches'] ?? [] as $branch): ?>
                <tr class="branch-row" data-branch-id="<?= $branch->getBranchId() ?>" 
                    data-city-id="<?= $branch->getCityId() ?>" 
                    data-location="<?= htmlspecialchars($branch->getLocation()) ?>" 
                    data-employees="<?= $branch->getNumberOfEmployees() ?>">
                    <td><?= htmlspecialchars($branch->getBranchId()) ?></td>
                    <td><?= htmlspecialchars($branch->getCityId()) ?></td>
                    <td><?= htmlspecialchars($branch->getLocation()) ?></td>
                    <td><?= htmlspecialchars($branch->getNumberOfEmployees()) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="tab" value="branches">
                            <input type="hidden" name="branch_id" value="<?= $branch->getBranchId() ?>">
                            <button type="submit" name="action" value="delete_branch" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button type="submit" name="action" value="add_branch" id="addBranchBtn">Add</button>
                    <button type="submit" name="action" value="delete_branch" class="delete-btn" id="deleteBranchBtn">Delete</button>
                    <button type="submit" name="action" value="save_branch" id="saveBranchBtn">Save</button>
                    <button type="submit" name="action" value="refresh_branch" class="refresh-btn">Refresh</button>
                    <button type="button" id="clearBranchForm" class="btn-secondary">Clear Form</button>
                </div>
            </form>
        </div>

        <!-- Production Tab -->
            <div id="production" class="tab-content <?= ($data['active_tab'] == 'production') ? 'active' : '' ?>">
            <h2>Production Management</h2>
        <form id="productionForm" method="POST">
            <input type="hidden" name="tab" value="production">
            <input type="hidden" id="editProductionId" name="production_id" value="">
            <div class="content-layout">
                <div class="form-section">
            <div class="form-grid">
                <div class="form-row">
                    <label for="supplier">Supplier:</label>
                    <input type="text" id="supplier" name="supplier" required>
                </div>
                <div class="form-row">
                    <label for="coffeeType">Coffee Type:</label>
                    <select id="coffeeType" name="coffee_type" required>
                        <option value="">Select Coffee Type</option>
                        <option value="Arabica Beans">Arabica Beans</option>
                        <option value="Robusta Beans">Robusta Beans</option>
                        <option value="Organic Beans">Organic Beans</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="productType">Product Type:</label>
                    <select id="productType" name="product_type" required>
                        <option value="">Select Product Type</option>
                        <option value="Ground">Ground</option>
                        <option value="Whole Bean">Whole Bean</option>
                        <option value="Instant">Instant</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="quantity">Quantity (kg):</label>
                    <input type="number" id="quantity" name="quantity" step="0.01" required>
                </div>
                <div class="form-row">
                    <label for="productionDate">Date:</label>
                    <input type="date" id="productionDate" name="production_date" required>
                </div>
            </div>
            
                    <!-- Moved action buttons here below the form data -->
            <div class="action-buttons">
                <button type="submit" name="action" value="save_production">Save</button>
                <button type="submit" name="action" value="refresh_production" class="refresh-btn">Refresh</button>
                <button type="button" id="clearProductionForm" class="btn-secondary">Clear Form</button>
            </div>
                </div>
                <div class="table-section">
            <table>
                <tr>
                    <th>Production ID</th>
                    <th>Branch ID</th>
                    <th>User ID</th>
                    <th>Supplier</th>
                    <th>Coffee Type</th>
                    <th>Product Type</th>
                    <th>Quantity (kg)</th>
                    <th>CO₂ Emissions (kg)</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            <?php foreach ($data['production'] ?? [] as $production):
                // Ensure $production is an object
                if (!is_object($production)) {
                    // Potentially log an error or skip this iteration
                    continue;
                }
            ?>
            <tr>
                <td><?= htmlspecialchars($production->getProductionId()) ?></td>
                <td><?= htmlspecialchars($production->getBranchId()) ?></td>
                <td><?= htmlspecialchars($production->getUserId()) ?></td>
                <td><?= htmlspecialchars($production->getSupplier()) ?></td>
                <td><?= htmlspecialchars($production->getCoffeeType()) ?></td>
                <td><?= htmlspecialchars($production->getProductType()) ?></td>
                <td><?= htmlspecialchars($production->getProductionQuantitiesOfCoffeeKG()) ?></td>
                <td><?= htmlspecialchars($production->getPrCarbonEmissionsKG()) ?></td>
                <td><?= htmlspecialchars(is_object($production->getActivityDate()) ? $production->getActivityDate()->format('Y-m-d') : $production->getActivityDate()) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                                    <input type="hidden" name="tab" value="production">
                        <input type="hidden" name="production_id" value="<?= $production->getProductionId() ?>">
                            <button type="submit" name="action" value="delete_production" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
                </div>
            </div>
        </form>
        </div>

        <!-- Packaging Tab -->
            <div id="packaging" class="tab-content <?= ($data['active_tab'] == 'packaging') ? 'active' : '' ?>">
            <h2>Packaging Management</h2>
        <form id="packagingForm" method="POST">
            <input type="hidden" name="tab" value="packaging">
            <input type="hidden" id="editPackagingId" name="packaging_id" value="">
            <div class="content-layout">
                <div class="form-section">
            <div class="form-grid">
                <div class="form-row">
                    <label for="packagingWaste">Packaging Waste (kg):</label>
                    <input type="number" id="packagingWaste" name="packaging_waste" step="0.01" required>
                </div>
                <div class="form-row">
                    <label for="packagingDate">Date:</label>
                    <input type="date" id="packagingDate" name="packaging_date" required>
                </div>
            </div>
            
                    <!-- Moved action buttons here below the form data -->
            <div class="action-buttons">
                <button type="submit" name="action" value="save_packaging">Save</button>
                <button type="submit" name="action" value="refresh_packaging" class="refresh-btn">Refresh</button>
                <button type="button" id="clearPackagingForm" class="btn-secondary">Clear Form</button>
            </div>
                </div>
                <div class="table-section">
            <table>
                <tr>
                    <th>Packaging ID</th>
                    <th>Branch ID</th>
                    <th>User ID</th>
                    <th>Waste (kg)</th>
                    <th>CO₂ Emissions (kg)</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            <?php foreach ($data['packaging'] ?? [] as $packaging):
                // Ensure $packaging is an object
                if (!is_object($packaging)) {
                    // Potentially log an error or skip this iteration
                    continue;
                }
            ?>
            <tr>
                <td><?= htmlspecialchars($packaging->getPackagingId()) ?></td>
                <td><?= htmlspecialchars($packaging->getBranchId()) ?></td>
                <td><?= htmlspecialchars($packaging->getUserId()) ?></td>
                <td><?= htmlspecialchars($packaging->getPackagingWasteKG()) ?></td>
                <td><?= htmlspecialchars($packaging->getPaCarbonEmissionsKG()) ?></td>
                <td><?= htmlspecialchars(is_object($packaging->getActivityDate()) ? $packaging->getActivityDate()->format('Y-m-d') : $packaging->getActivityDate()) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                                    <input type="hidden" name="tab" value="packaging">
                        <input type="hidden" name="packaging_id" value="<?= $packaging->getPackagingId() ?>">
                            <button type="submit" name="action" value="delete_packaging" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
                </div>
            </div>
        </form>
        </div>

        <!-- Distribution Tab -->
            <div id="distribution" class="tab-content <?= ($data['active_tab'] == 'distribution') ? 'active' : '' ?>">
            <h2>Distribution Management</h2>
        <form id="distributionForm" method="POST">
            <input type="hidden" name="tab" value="distribution">
            <input type="hidden" id="editDistributionId" name="distribution_id" value="">
            <div class="content-layout">
                <div class="form-section">
            <div class="form-grid">
                <div class="form-row">
                    <label for="vehicleType">Vehicle Type:</label>
                    <select id="vehicleType" name="vehicle_type" required>
                        <option value="">Select Vehicle Type</option>
                        <option value="Minivan">Minivan</option>
                        <option value="Pickup Truck">Pickup Truck</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="numberOfVehicles">Number of Vehicles:</label>
                    <input type="number" id="numberOfVehicles" name="num_vehicles" required>
                </div>
                <div class="form-row">
                    <label for="distancePerVehicle">Distance per Vehicle (km):</label>
                    <input type="number" id="distancePerVehicle" name="distance_per_vehicle" step="0.01" required>
                </div>
                <div class="form-row">
                    <label for="distributionDate">Date:</label>
                    <input type="date" id="distributionDate" name="distribution_date" required>
                </div>
            </div>
            
                    <!-- Moved action buttons here below the form data -->
            <div class="action-buttons">
                <button type="submit" name="action" value="save_distribution">Save</button>
                <button type="submit" name="action" value="refresh_distribution" class="refresh-btn">Refresh</button>
                <button type="button" id="clearDistributionForm" class="btn-secondary">Clear Form</button>
            </div>
                </div>
                <div class="table-section">
            <table>
                <tr>
                    <th>Distribution ID</th>
                    <th>Branch ID</th>
                    <th>User ID</th>
                    <th>Vehicle Type</th>
                    <th>Num Vehicles</th>
                    <th>Dist/Vehicle (km)</th>
                    <th>Total Dist (km)</th>
                    <th>Fuel Eff.</th>
                    <th>CO₂ Emissions (kg)</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            <?php foreach ($data['distribution'] ?? [] as $distribution):
                // Ensure $distribution is an object
                if (!is_object($distribution)) {
                    // Potentially log an error or skip this iteration
                    continue;
                }
            ?>
            <tr>
                <td><?= htmlspecialchars($distribution->getDistributionId()) ?></td>
                <td><?= htmlspecialchars($distribution->getBranchId()) ?></td>
                <td><?= htmlspecialchars($distribution->getUserId()) ?></td>
                <td><?= htmlspecialchars($distribution->getVehicleType()) ?></td>
                <td><?= htmlspecialchars($distribution->getNumberOfVehicles()) ?></td>
                <td><?= htmlspecialchars($distribution->getDistancePerVehicleKM()) ?></td>
                <td><?= htmlspecialchars($distribution->getTotalDistanceKM()) ?></td>
                <td><?= htmlspecialchars($distribution->getFuelEfficiency()) ?></td>
                <td><?= htmlspecialchars($distribution->getVCarbonEmissionsKg()) ?></td>
                <td><?= htmlspecialchars(is_object($distribution->getActivityDate()) ? $distribution->getActivityDate()->format('Y-m-d') : $distribution->getActivityDate()) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                                    <input type="hidden" name="tab" value="distribution">
                        <input type="hidden" name="distribution_id" value="<?= $distribution->getDistributionId() ?>">
                            <button type="submit" name="action" value="delete_distribution" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
                </div>
            </div>
        </form>
        </div>

        <!-- Report Tab -->
    <div id="report" class="tab-content <?= ($data['active_tab'] == 'report') ? 'active' : '' ?>">
            <h2>Carbon Footprint Reports</h2>
        
        <!-- Summary Dashboard -->
        <div class="dashboard-summary">
            <h3>Overall Carbon Emissions</h3>
            <div class="metrics-container">
                <div class="metric-card">
                    <i class="fas fa-industry metric-icon"></i>
                    <div class="metric-value"><?= number_format($data['metrics']['production_emissions'] ?? 0, 2) ?> kg</div>
                    <div class="metric-label">Production</div>
                </div>
                <div class="metric-card">
                    <i class="fas fa-box metric-icon"></i>
                    <div class="metric-value"><?= number_format($data['metrics']['packaging_emissions'] ?? 0, 2) ?> kg</div>
                    <div class="metric-label">Packaging</div>
                </div>
                <div class="metric-card">
                    <i class="fas fa-truck metric-icon"></i>
                    <div class="metric-value"><?= number_format($data['metrics']['distribution_emissions'] ?? 0, 2) ?> kg</div>
                    <div class="metric-label">Distribution</div>
                </div>
                <div class="metric-card total">
                    <i class="fas fa-globe metric-icon"></i>
                    <div class="metric-value"><?= number_format($data['metrics']['total_emissions'] ?? 0, 2) ?> kg</div>
                    <div class="metric-label">Total Emissions</div>
                </div>
            </div>
        </div>
        
        <p>Select a branch to generate and download its carbon report:</p>
            
            <table>
                <tr>
                    <th>Branch ID</th>
                    <th>Location</th>
                <th>Total Emissions</th>
                    <th>Action</th>
                </tr>
            <?php foreach ($data['branches'] ?? [] as $branch): 
                // Find branch emissions from metrics if available
                $branchEmissions = 0;
                foreach (($data['metrics']['branches'] ?? []) as $branchMetric) {
                    if ($branchMetric['branch_id'] == $branch->getBranchId()) {
                        $branchEmissions = $branchMetric['total_emissions'];
                        break;
                    }
                }
            ?>
                <tr>
                <td><?= htmlspecialchars($branch->getBranchId()) ?></td>
                <td><?= htmlspecialchars($branch->getLocation()) ?></td>
            <td><?= number_format($branchEmissions, 2) ?> kg CO2</td>
                <td class="action-buttons-container">
                    <a href="?controller=report&action=download&branch_id=<?= $branch->getBranchId() ?>" class="download-link">
                        <i class="fas fa-download"></i> Download Report
                    </a>
                    <a href="?controller=report&action=view&branch_id=<?= $branch->getBranchId() ?>" class="view-link">
                        <i class="fas fa-eye"></i> View Report
                    </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            
        <!-- Branch Comparison Chart -->
        <div class="chart-container">
            <h3>Branch Emissions Comparison</h3>
            <canvas id="branchComparisonChart"></canvas>
        </div>
        
        <style>
            .view-link {
                display: inline-block;
                margin-left: 10px;
                padding: 3px 10px;
                background-color: #2196F3;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                font-weight: bold;
            }
            .view-link:hover {
                background-color: #0b7dda;
                color: white;
            }
            
            .dashboard-summary {
                margin-bottom: 30px;
                background-color: #f9f9f9;
                border-radius: 8px;
                padding: 15px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .metrics-container {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 15px;
                margin-top: 15px;
            }
            
            .metric-card {
                flex: 1;
                min-width: 200px;
                background-color: white;
                border-radius: 8px;
                padding: 15px;
                text-align: center;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                border-left: 4px solid #4CAF50;
            }
            
            .metric-card.total {
                border-left: 4px solid #f44336;
                background-color: #f5f5f5;
            }
            
            .metric-icon {
                font-size: 24px;
                color: #4CAF50;
                margin-bottom: 10px;
            }
            
            .metric-card.total .metric-icon {
                color: #f44336;
            }
            
            .metric-value {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 5px;
            }
            
            .metric-label {
                color: #666;
                font-size: 14px;
            }
            
            .chart-container {
                margin-top: 30px;
                background-color: white;
                border-radius: 8px;
                padding: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .action-buttons-container {
                display: flex;
                gap: 10px;
            }
            
            @media (max-width: 768px) {
                .metrics-container {
                    flex-direction: column;
                }
                .metric-card {
                    min-width: 100%;
                }
            }
        </style>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="footer">
    <p>&copy; <?= date('Y') ?> CofaktoryCFT - Carbon Footprint Tracker</p>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Initialize the dashboard functionality when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Set up tab navigation
        setupTabNavigation();
        
        // Set up form functionality
        setupFormInteractions();
        
        // Set up charts if needed
        setupCharts();
        
        // Set up alert handling
        setupAlerts();
        
        // Show welcome toast message
        showToast('Welcome to the OP Manager Dashboard', 'info');
    });
    
    // Function to handle tab navigation
    function setupTabNavigation() {
        // Get all tab buttons
        const tabButtons = document.querySelectorAll('.tabs button');
        
        // Add click event to each tab button
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.textContent.trim().split(' ').pop().toLowerCase();
                showTab(tabName);
            });
        });
    }
    
    // Function to show a specific tab
        function showTab(tabName) {
        // Save the active tab to a cookie for persistence
        document.cookie = "active_tab=" + tabName + "; path=/";
        
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
        // Show selected tab with animation
        const selectedTab = document.getElementById(tabName);
        selectedTab.classList.add('active');
        
        // Add fade-in animation
        selectedTab.style.animation = 'none';
        setTimeout(() => {
            selectedTab.style.animation = 'fadeIn 0.5s ease';
        }, 10);
            
            // Update active tab button
            document.querySelectorAll('.tabs button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Find the button that was clicked and add active class
            document.querySelectorAll('.tabs button').forEach(button => {
                if (button.textContent.toLowerCase().includes(tabName.toLowerCase())) {
                    button.classList.add('active');
                }
            });
        
        // Update hidden tab input fields in all forms to maintain the active tab
        document.querySelectorAll('form input[name="tab"]').forEach(input => {
            input.value = tabName;
        });
        
        // Show a toast notification
        showToast(`Switched to ${tabName.charAt(0).toUpperCase() + tabName.slice(1)} tab`, 'info');
    }
    
    // Function to set up form interactions (row selection, form clearing, etc.)
    function setupFormInteractions() {
        // Initialize date fields with today's date
            const today = new Date().toISOString().split('T')[0];
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.value = today;
            });
        
        // Add animation to form fields
        document.querySelectorAll('input, select, textarea').forEach(element => {
            element.addEventListener('focus', function() {
                this.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    this.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            });
            });
        
        // User table row click event
        setupUserRowClicks();
        
        // Branch table row click event
        setupBranchRowClicks();
        
        // Add data attributes to production, packaging, and distribution rows
        setupDataRowAttributes();
        
        // Production row click events
        setupProductionRowClicks();
        
        // Packaging row click events
        setupPackagingRowClicks();
        
        // Distribution row click events
        setupDistributionRowClicks();
        
        // Add animation to buttons
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', function() {
                // Skip for tab buttons
                if (this.parentElement.classList.contains('tabs')) return;
                
                // Apply animation
                this.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    this.classList.remove('animate__animated', 'animate__pulse');
                }, 500);
            });
        });
    }
    
    // Function to set up user row clicks
    function setupUserRowClicks() {
        const userRows = document.querySelectorAll('#usersTable .user-row');
        userRows.forEach(row => {
            row.addEventListener('click', function() {
                // Get data from row
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                const userRole = this.getAttribute('data-user-role');
                const userEmail = this.getAttribute('data-user-email');
                const branchId = this.getAttribute('data-branch-id');
                const forcePasswordChange = this.getAttribute('data-force-password-change') === '1';
                
                // Populate form fields
                document.getElementById('editUserId').value = userId;
                document.getElementById('userName').value = userName;
                document.getElementById('userRole').value = userRole;
                document.getElementById('userEmail').value = userEmail;
                document.getElementById('userPassword').value = '********'; // Don't show actual password
                document.getElementById('userPassword').required = false; // Not required when editing
                
                if (branchId) {
                    document.getElementById('userBranch').value = branchId;
                } else {
                    document.getElementById('userBranch').value = '';
                }
                
                document.getElementById('forcePasswordChange').checked = forcePasswordChange;
                
                // Highlight selected row
                userRows.forEach(r => r.classList.remove('selected-row'));
                this.classList.add('selected-row');
                
                // Add animation to selected row
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'pulse-highlight 1s ease';
                }, 10);
                
                // Show toast notification
                showToast(`Selected user: ${userName}`, 'info');
            });
        });
        
        // Clear user form
        document.getElementById('clearUserForm').addEventListener('click', function() {
            document.getElementById('userForm').reset();
            document.getElementById('editUserId').value = '';
            document.getElementById('userPassword').required = true;
            document.querySelectorAll('#usersTable .user-row').forEach(r => r.classList.remove('selected-row'));
            showToast('User form cleared', 'info');
        });
    }
        
    // Function to set up branch row clicks
    function setupBranchRowClicks() {
        const branchRows = document.querySelectorAll('#branchesTable .branch-row');
        branchRows.forEach(row => {
            row.addEventListener('click', function() {
                // Get data from row
                const branchId = this.getAttribute('data-branch-id');
                const cityId = this.getAttribute('data-city-id');
                const location = this.getAttribute('data-location');
                const employees = this.getAttribute('data-employees');
                
                // Populate form fields
                document.getElementById('editBranchId').value = branchId;
                document.getElementById('cityId').value = cityId;
                document.getElementById('branchLocation').value = location;
                document.getElementById('numEmployees').value = employees;
                
                // Highlight selected row
                branchRows.forEach(r => r.classList.remove('selected-row'));
                this.classList.add('selected-row');
                
                // Add animation to selected row
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'pulse-highlight 1s ease';
                }, 10);
                
                // Show toast notification
                showToast(`Selected branch: ${location}`, 'info');
            });
        });
        
        // Clear branch form
        document.getElementById('clearBranchForm').addEventListener('click', function() {
            document.getElementById('branchForm').reset();
            document.getElementById('editBranchId').value = '';
            document.querySelectorAll('#branchesTable .branch-row').forEach(r => r.classList.remove('selected-row'));
            showToast('Branch form cleared', 'info');
        });
    }
        
    // Function to set up data attributes for all rows
    function setupDataRowAttributes() {
        document.querySelectorAll('table tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                const idCell = cells[0];
                if (idCell && idCell.textContent) {
                    const id = idCell.textContent.trim();
                    if (row.closest('#production')) {
                        row.classList.add('production-row');
                        row.setAttribute('data-production-id', id);
                        if (cells.length >= 9) {
                            row.setAttribute('data-supplier', cells[3].textContent.trim());
                            row.setAttribute('data-coffee-type', cells[4].textContent.trim());
                            row.setAttribute('data-product-type', cells[5].textContent.trim());
                            row.setAttribute('data-quantity', cells[6].textContent.trim());
                            row.setAttribute('data-date', cells[8].textContent.trim());
                        }
                    } else if (row.closest('#packaging')) {
                        row.classList.add('packaging-row');
                        row.setAttribute('data-packaging-id', id);
                        if (cells.length >= 6) {
                            row.setAttribute('data-waste', cells[3].textContent.trim());
                            row.setAttribute('data-date', cells[5].textContent.trim());
                        }
                    } else if (row.closest('#distribution')) {
                        row.classList.add('distribution-row');
                        row.setAttribute('data-distribution-id', id);
                        if (cells.length >= 10) {
                            row.setAttribute('data-vehicle-type', cells[3].textContent.trim());
                            row.setAttribute('data-num-vehicles', cells[4].textContent.trim());
                            row.setAttribute('data-distance', cells[5].textContent.trim());
                            row.setAttribute('data-date', cells[9].textContent.trim());
                        }
                    }
                }
            }
        });
    }
        
    // Function to set up production row clicks
    function setupProductionRowClicks() {
        const productionRows = document.querySelectorAll('.production-row');
        productionRows.forEach(row => {
            row.addEventListener('click', function() {
                const productionId = this.getAttribute('data-production-id');
                const supplier = this.getAttribute('data-supplier');
                const coffeeType = this.getAttribute('data-coffee-type');
                const productType = this.getAttribute('data-product-type');
                const quantity = this.getAttribute('data-quantity');
                const date = this.getAttribute('data-date');
                
                // Populate form fields
                document.getElementById('editProductionId').value = productionId;
                document.getElementById('supplier').value = supplier;
                document.getElementById('coffeeType').value = coffeeType;
                document.getElementById('productType').value = productType;
                document.getElementById('quantity').value = quantity;
                document.getElementById('productionDate').value = formatDateForInput(date);
                
                // Highlight selected row
                productionRows.forEach(r => r.classList.remove('selected-row'));
                this.classList.add('selected-row');
                
                // Show toast notification
                showToast(`Selected production record: ${supplier} - ${coffeeType}`, 'info');
            });
        });
        
        // Clear production form
        document.getElementById('clearProductionForm')?.addEventListener('click', function() {
            document.getElementById('productionForm').reset();
            document.getElementById('editProductionId').value = '';
            document.querySelectorAll('.production-row').forEach(r => r.classList.remove('selected-row'));
            showToast('Production form cleared', 'info');
        });
    }
        
    function setupPackagingRowClicks() {
        const packagingRows = document.querySelectorAll('.packaging-row');
        packagingRows.forEach(row => {
            row.addEventListener('click', function() {
                const packagingId = this.getAttribute('data-packaging-id');
                const waste = this.getAttribute('data-waste');
                const date = this.getAttribute('data-date');
                
                // Populate form fields
                document.getElementById('editPackagingId').value = packagingId;
                document.getElementById('packagingWaste').value = waste;
                document.getElementById('packagingDate').value = formatDateForInput(date);
                
                // Highlight selected row
                packagingRows.forEach(r => r.classList.remove('selected-row'));
                this.classList.add('selected-row');
                
                // Add animation to selected row
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'pulse-highlight 1s ease';
                }, 10);
                
                // Show toast notification
                showToast(`Selected packaging record ID: ${packagingId}`, 'info');
            });
        });
        
        // Clear packaging form with toast
        document.getElementById('clearPackagingForm')?.addEventListener('click', function() {
            document.getElementById('packagingForm').reset();
            document.getElementById('editPackagingId').value = '';
            document.querySelectorAll('.packaging-row').forEach(r => r.classList.remove('selected-row'));
            showToast('Packaging form cleared', 'info');
        });
    }
        
    function setupDistributionRowClicks() {
        const distributionRows = document.querySelectorAll('.distribution-row');
        distributionRows.forEach(row => {
            row.addEventListener('click', function() {
                const distributionId = this.getAttribute('data-distribution-id');
                const vehicleType = this.getAttribute('data-vehicle-type');
                const numVehicles = this.getAttribute('data-num-vehicles');
                const distance = this.getAttribute('data-distance');
                const date = this.getAttribute('data-date');
                
                // Populate form fields
                document.getElementById('editDistributionId').value = distributionId;
                document.getElementById('vehicleType').value = vehicleType;
                document.getElementById('numberOfVehicles').value = numVehicles;
                document.getElementById('distancePerVehicle').value = distance;
                document.getElementById('distributionDate').value = formatDateForInput(date);
                
                // Highlight selected row
                distributionRows.forEach(r => r.classList.remove('selected-row'));
                this.classList.add('selected-row');
                
                // Add animation to selected row
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'pulse-highlight 1s ease';
                }, 10);
                
                // Show toast notification
                showToast(`Selected distribution record: ${vehicleType}`, 'info');
            });
        });
        
        // Clear distribution form with toast
        document.getElementById('clearDistributionForm')?.addEventListener('click', function() {
            document.getElementById('distributionForm').reset();
            document.getElementById('editDistributionId').value = '';
            document.querySelectorAll('.distribution-row').forEach(r => r.classList.remove('selected-row'));
            showToast('Distribution form cleared', 'info');
        });
    }
    
    // Function to set up chart visualizations
    function setupCharts() {
        // Check if we're on the reports tab and the chart canvas exists
        if (document.getElementById('branchComparisonChart')) {
            const ctx = document.getElementById('branchComparisonChart').getContext('2d');
            
            // Extract branch data from the table
            const branchData = [];
            const branchLabels = [];
            const tableRows = document.querySelectorAll('#report table tr:not(:first-child)');
            
            tableRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 3) {
                    const branchName = cells[1].textContent.trim();
                    const emissionsText = cells[2].textContent.trim();
                    const emissions = parseFloat(emissionsText.replace(/[^\d.-]/g, ''));
                    
                    branchLabels.push(branchName);
                    branchData.push(emissions);
                }
            });
            
            // Create gradient colors for the chart
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(76, 175, 80, 0.8)');
            gradient.addColorStop(1, 'rgba(76, 175, 80, 0.2)');
            
            // Create the chart with animation
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: branchLabels,
                    datasets: [{
                        label: 'Carbon Emissions (kg CO2)',
                        data: branchData,
                        backgroundColor: gradient,
                        borderColor: 'rgba(76, 175, 80, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                        hoverBackgroundColor: 'rgba(76, 175, 80, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Carbon Emissions by Branch',
                            font: {
                                family: "'Poppins', sans-serif",
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                family: "'Poppins', sans-serif",
                                size: 14
                            },
                            bodyFont: {
                                family: "'Poppins', sans-serif",
                                size: 13
                            },
                            padding: 15,
                            cornerRadius: 8,
                            caretSize: 6,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 12
                                }
                            },
                            title: {
                                display: true,
                                text: 'Carbon Emissions (kg CO2)',
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 13,
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 12
                                }
                            },
                            title: {
                                display: true,
                                text: 'Branch Location',
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 13,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
            
            showToast('Branch comparison chart generated', 'success');
        }
    }
    
    // Function to set up alert handling
    function setupAlerts() {
        // Handle alert close buttons
        document.querySelectorAll('.alert .close-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const alert = this.parentElement;
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            });
        });
        
        // Auto-dismiss alerts after 8 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 8000);
        });
    }
        
        // Helper function to format date for input fields
        function formatDateForInput(dateString) {
            try {
                const date = new Date(dateString);
                return date.toISOString().split('T')[0];
            } catch (e) {
                return '';
            }
        }
    
    // Toast notification system
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Set icon based on type
        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'error') icon = 'exclamation-circle';
        if (type === 'warning') icon = 'exclamation-triangle';
        
        toast.innerHTML = `
            <span class="toast-icon"><i class="fas fa-${icon}"></i></span>
            <span>${message}</span>
        `;
        
        toastContainer.appendChild(toast);
        
        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toastContainer.removeChild(toast);
            }, 500);
        }, 3000);
    }
    </script>
</body>
</html>