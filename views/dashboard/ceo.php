<?php
// ceo_dashboard_view.php
?>
<!DOCTYPE html>
<html>
<head>
    <title>CEO Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .footer {
            background: linear-gradient(135deg, #333, #222);
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
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
            height: 400px;
            position: relative;
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
        
        /* Grid layout for dashboard sections */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
            grid-auto-rows: minmax(400px, auto);
            overflow: hidden;
        }
        
        .grid-span-2 {
            grid-column: span 2;
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
        
        /* Responsive styling */
        @media (max-width: 768px) {
            .metrics-container {
                flex-direction: column;
            }
            .metric-card {
                min-width: 100%;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
                grid-auto-rows: 350px;
            }
            .grid-span-2 {
                grid-column: span 1;
            }
            .tabs {
                flex-wrap: wrap;
            }
            .tabs button {
                flex-basis: 50%;
            }
            .chart-container {
                height: 350px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="header-logo">
            <img src="<?= APP_URL ?>/img/coffee_eco_logo.svg" alt="Logo" width="50" height="50" class="animate__animated animate__fadeIn">
            <div class="header-title">
                <h1><i class="fas fa-leaf"></i> Coffee Factory Carbon Tracker</h1>
                <h2>Chief Executive Officer Dashboard</h2>
            </div>
        </div>
        <div class="user-info animate__animated animate__fadeIn">
            <p><i class="fas fa-user-circle"></i> <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User' ?></p>
            <p><i class="fas fa-user-tie"></i> Role: CEO</p>
        </div>
        <a href="<?= APP_URL ?>/?controller=auth&action=logout" class="animate__animated animate__fadeIn">
            <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log Out</button>
        </a>
    </div>

    <div class="container">
        <!-- Toast Container for Notifications -->
        <div class="toast-container" id="toastContainer"></div>
        
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
        
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?>">
                <i class="fas fa-info-circle"></i> <?= $_SESSION['flash_message'] ?>
                <span class="close-btn">&times;</span>
            </div>
            <?php unset($_SESSION['flash_message']); unset($_SESSION['flash_type']); ?>
        <?php endif; ?>
        
        <div class="tabs animate__animated animate__fadeIn">
            <button class="active" onclick="showTab('overview')"><i class="fas fa-chart-pie"></i> Overview</button>
            <button onclick="showTab('emissions')"><i class="fas fa-smog"></i> Emissions Analysis</button>
            <button onclick="showTab('strategies')"><i class="fas fa-leaf"></i> Reduction Strategies</button>
            <button onclick="showTab('performance')"><i class="fas fa-tachometer-alt"></i> Branch Performance</button>
        </div>

        <!-- Overview Tab -->
        <div id="overview" class="tab-content active">
            <h2>Company Carbon Footprint Overview</h2>
            
            <!-- Summary Metrics -->
            <div class="dashboard-summary">
                <h3>Key Performance Indicators</h3>
                <div class="metrics-container">
                    <div class="metric-card">
                        <div class="metric-icon">
                            <i class="fas fa-industry"></i>
                        </div>
                        <div class="metric-value"><?= number_format($prodTotal, 2) ?> kg</div>
                        <div class="metric-label">Production Emissions</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="metric-value"><?= number_format($packTotal, 2) ?> kg</div>
                        <div class="metric-label">Packaging Emissions</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="metric-value"><?= number_format($distTotal, 2) ?> kg</div>
                        <div class="metric-label">Distribution Emissions</div>
                    </div>
                    <div class="metric-card total">
                        <div class="metric-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="metric-value"><?= number_format($prodTotal + $packTotal + $distTotal, 2) ?> kg</div>
                        <div class="metric-label">Total Emissions</div>
                    </div>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="dashboard-grid">
        <div class="chart-container">
                    <h3><i class="fas fa-chart-line"></i> Emissions Over Time</h3>
            <canvas id="emissionsOverTimeChart"></canvas>
        </div>

                <div class="chart-container">
                    <h3><i class="fas fa-chart-pie"></i> Emissions By Process</h3>
                    <canvas id="emissionsByProcessChart"></canvas>
                </div>
                
        <div class="chart-container">
                    <h3><i class="fas fa-city"></i> Emissions By City</h3>
            <canvas id="emissionsByCityChart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3><i class="fas fa-tasks"></i> Reduction Plans Status</h3>
                    <canvas id="reductionPlansStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Emissions Analysis Tab -->
        <div id="emissions" class="tab-content">
            <h2>Detailed Emissions Analysis</h2>
            
            <div class="dashboard-summary">
                <h3>Emission Distribution</h3>
                <p>This section provides a detailed breakdown of carbon emissions across different aspects of the business.</p>
            </div>
            
            <div class="dashboard-grid">
                <div class="chart-container grid-span-2">
                    <h3><i class="fas fa-building"></i> Emissions Per Branch</h3>
                    <canvas id="emissionsPerBranchChart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3><i class="fas fa-user-friends"></i> Emissions Per Employee</h3>
                    <canvas id="emissionsPerEmployeeChart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3><i class="fas fa-map-marker-alt"></i> City Emissions Distribution</h3>
                    <canvas id="cityDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Reduction Strategies Tab -->
        <div id="strategies" class="tab-content">
            <h2>Carbon Reduction Strategies</h2>
            
            <div class="dashboard-summary">
                <h3>Strategy Implementation & ROI</h3>
                <p>This section analyzes the financial aspects and implementation status of carbon reduction strategies.</p>
            </div>
            
            <div class="dashboard-grid">
                <div class="chart-container">
                    <h3><i class="fas fa-money-bill-wave"></i> Projected Profits by City</h3>
                    <canvas id="projectedProfitsChart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3><i class="fas fa-coins"></i> Implementation Costs by City</h3>
                    <canvas id="implementationCostsChart"></canvas>
                </div>
                
                <div class="chart-container grid-span-2">
                    <h3><i class="fas fa-check-circle"></i> Strategy Status Distribution</h3>
                    <canvas id="strategyStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Branch Performance Tab -->
        <div id="performance" class="tab-content">
            <h2>Branch Performance Analysis</h2>
            
            <div class="dashboard-summary">
                <h3>Top & Bottom Performers</h3>
                <p>This section highlights the best and worst performing branches in terms of carbon emissions.</p>
            </div>
            
            <div class="dashboard-grid">
                <div class="chart-container">
                    <h3><i class="fas fa-trophy"></i> Top 5 Lowest Emission Branches</h3>
                    <canvas id="topBestBranchesChart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3><i class="fas fa-exclamation-triangle"></i> Top 5 Highest Emission Branches</h3>
                    <canvas id="topWorstBranchesChart"></canvas>
                </div>
                
                <div class="chart-container grid-span-2">
                    <h3><i class="fas fa-chart-bar"></i> Branch Efficiency Comparison</h3>
                    <canvas id="branchComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <p>&copy; <?= date('Y') ?> CofaktoryCFT - Carbon Footprint Tracker</p>
        </div>

        <script>
        // Initialize the dashboard functionality when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Set up tab navigation
            setupTabNavigation();
            
            // Set up alert handling
            setupAlerts();
            
            // Initialize charts
            initializeCharts();
            
            // Show welcome toast message
            showToast('Welcome to the CEO Dashboard', 'info');
        });
        
        // Function to handle tab navigation
        function setupTabNavigation() {
            // Add click event to each tab button (already set via onclick attributes)
            document.querySelectorAll('.tabs button').forEach(button => {
                button.addEventListener('click', function() {
                    // Get tab name from onclick attribute
                    const tabName = this.getAttribute('onclick').match(/showTab\('(.+?)'\)/)[1];
                    showTab(tabName);
                });
            });
        }
        
        // Function to show a specific tab
        function showTab(tabName) {
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
            
            // Show a toast notification
            showToast(`Switched to ${tabName.charAt(0).toUpperCase() + tabName.slice(1)} tab`, 'info');
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
        
        // Function to initialize all charts
        function initializeCharts() {
            // Shared chart options
            const barOptions = {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(200, 200, 200, 0.2)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        displayColors: false
                    },
                    legend: {
                        labels: {
                            font: {
                                size: 13
                            }
                        },
                        position: 'top'
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            };
            
            // Pie chart options
            const pieOptions = {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                size: 13
                            },
                            padding: 15
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                }
            };
            
            // Initialize all charts
            initEmissionsOverTimeChart();
            initEmissionsByProcessChart();
            initEmissionsByCityChart();
            initReductionPlansStatusChart();
            initEmissionsPerBranchChart();
            initEmissionsPerEmployeeChart();
            initCityDistributionChart();
            initProjectedProfitsChart();
            initImplementationCostsChart();
            initStrategyStatusChart();
            initTopBestBranchesChart();
            initTopWorstBranchesChart();
            initBranchComparisonChart();
            
            // Function to initialize Emissions Over Time Chart
            function initEmissionsOverTimeChart() {
                const emissionsOverTimeData = <?php echo $monthlyEmissionsJson; ?>;
                const ctx = document.getElementById('emissionsOverTimeChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: Object.keys(emissionsOverTimeData),
                        datasets: [{
                            label: 'Total Emissions',
                            data: Object.values(emissionsOverTimeData),
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#3498db',
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Emissions (kg CO2)',
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    color: 'rgba(200, 200, 200, 0.2)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14
                                },
                                bodyFont: {
                                    size: 13
                                },
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return `Emissions: ${context.raw.toFixed(2)} kg CO2`;
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 1500
                        }
                    }
                });
            }
            
            // Function to initialize Emissions By Process Chart
            function initEmissionsByProcessChart() {
                const processData = <?php echo $processTotalsJson; ?>;
                const ctx = document.getElementById('emissionsByProcessChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(processData),
                        datasets: [{
                            data: Object.values(processData),
                            backgroundColor: [
                                '#3498db',
                                '#9b59b6',
                                '#e67e22'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: pieOptions
                });
            }
            
            // Function to initialize Emissions By City Chart
            function initEmissionsByCityChart() {
                const cityEmissionsData = <?php echo $cityEmissionsJson; ?>;
                const ctx = document.getElementById('emissionsByCityChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(cityEmissionsData),
                        datasets: [{
                            label: 'Total Emissions',
                            data: Object.values(cityEmissionsData),
                            backgroundColor: '#2ecc71',
                            borderRadius: 6,
                            hoverBackgroundColor: 'rgba(46, 204, 113, 0.9)'
                        }]
                    },
                    options: barOptions
                });
            }
            
            // Function to initialize Reduction Plans Status Chart
            function initReductionPlansStatusChart() {
                const planStatusData = <?php echo $planStatusJson; ?>;
                const ctx = document.getElementById('reductionPlansStatusChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(planStatusData),
                        datasets: [{
                            data: Object.values(planStatusData),
                            backgroundColor: [
                                '#f1c40f',
                                '#2ecc71',
                                '#e74c3c'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: pieOptions
                });
            }
            
            // Function to initialize Emissions Per Branch Chart
            function initEmissionsPerBranchChart() {
                const branchEmissionsData = <?php echo $branchEmissionsJson; ?>;
                const branchNames = <?php echo $branchNamesJson; ?>;
                const ctx = document.getElementById('emissionsPerBranchChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.values(branchNames),
                        datasets: [{
                            label: 'Total Emissions',
                            data: Object.values(branchEmissionsData),
                            backgroundColor: '#3498db', // Blue
                            borderRadius: 4
                        }]
                    },
                    options: barOptions
                });
            }
            
            // Function to initialize Emissions Per Employee Chart
            function initEmissionsPerEmployeeChart() {
                const emissionsPerEmployeeData = <?php echo $emissionsPerEmployeeJson; ?>;
                const branchNames = <?php echo $branchNamesJson; ?>;
                const ctx = document.getElementById('emissionsPerEmployeeChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.values(branchNames),
                        datasets: [{
                            label: 'Emissions per Employee',
                            data: Object.values(emissionsPerEmployeeData),
                            backgroundColor: '#9b59b6', // Purple
                            borderRadius: 4
                        }]
                    },
                    options: barOptions
                });
            }
            
            // Function to initialize City Distribution Chart (duplicate of Emissions By City but with different colors)
            function initCityDistributionChart() {
                const cityEmissionsData = <?php echo $cityEmissionsJson; ?>;
                const ctx = document.getElementById('cityDistributionChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(cityEmissionsData),
                        datasets: [{
                            data: Object.values(cityEmissionsData),
                            backgroundColor: [
                                '#3498db',
                                '#e74c3c',
                                '#2ecc71',
                                '#f1c40f'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: pieOptions
                });
            }
            
            // Function to initialize Projected Profits Chart
            function initProjectedProfitsChart() {
                const cityProfitsData = <?php echo $cityProfitsJson; ?>;
                const ctx = document.getElementById('projectedProfitsChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(cityProfitsData),
                        datasets: [{
                            label: 'Projected Annual Profits',
                            data: Object.values(cityProfitsData),
                            backgroundColor: '#f1c40f', // Yellow
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...barOptions,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Profits ($)'
                                }
                            }
                        }
                    }
                });
            }
            
            // Function to initialize Implementation Costs Chart
            function initImplementationCostsChart() {
                const cityCostsData = <?php echo $cityCostsJson; ?>;
                const ctx = document.getElementById('implementationCostsChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(cityCostsData),
                        datasets: [{
                            label: 'Implementation Costs',
                            data: Object.values(cityCostsData),
                            backgroundColor: '#e74c3c', // Red
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...barOptions,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Costs ($)'
                                }
                            }
                        }
                    }
                });
            }
            
            // Function to initialize Strategy Status Chart (duplicate of Reduction Plans Status)
            function initStrategyStatusChart() {
                const planStatusData = <?php echo $planStatusJson; ?>;
                const ctx = document.getElementById('strategyStatusChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(planStatusData),
                        datasets: [{
                            label: 'Number of Plans',
                            data: Object.values(planStatusData),
                            backgroundColor: [
                                '#f1c40f',
                                '#2ecc71',
                                '#e74c3c'
                            ],
                            borderRadius: 4
                        }]
                    },
                    options: barOptions
                });
            }
            
            // Function to initialize Top Best Branches Chart
            function initTopBestBranchesChart() {
                const bestBranchesData = <?php echo $bestBranchesJson; ?>;
                const branchNames = <?php echo $branchNamesJson; ?>;
                const bestBranchesLabels = Object.keys(bestBranchesData).map(id => branchNames[id]);
                const ctx = document.getElementById('topBestBranchesChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: bestBranchesLabels,
                        datasets: [{
                            label: 'Emissions',
                            data: Object.values(bestBranchesData),
                            backgroundColor: '#2ecc71', // Green
                            borderRadius: 4
                        }]
                    },
                    options: barOptions
                });
            }
            
            // Function to initialize Top Worst Branches Chart
            function initTopWorstBranchesChart() {
                const worstBranchesData = <?php echo $worstBranchesJson; ?>;
                const branchNames = <?php echo $branchNamesJson; ?>;
                const worstBranchesLabels = Object.keys(worstBranchesData).map(id => branchNames[id]);
                const ctx = document.getElementById('topWorstBranchesChart').getContext('2d');
                
                new Chart(ctx, {
                type: 'bar',
                data: {
                        labels: worstBranchesLabels,
                    datasets: [{
                        label: 'Emissions',
                            data: Object.values(worstBranchesData),
                            backgroundColor: '#e74c3c', // Red
                            borderRadius: 4
                        }]
                    },
                    options: barOptions
                });
            }
            
            // Function to initialize Branch Comparison Chart (combining emissions per branch with per employee)
            function initBranchComparisonChart() {
                const branchEmissionsData = <?php echo $branchEmissionsJson; ?>;
                const emissionsPerEmployeeData = <?php echo $emissionsPerEmployeeJson; ?>;
                const branchNames = <?php echo $branchNamesJson; ?>;
                const ctx = document.getElementById('branchComparisonChart').getContext('2d');
                
                // Scale the per employee data to be visible alongside total emissions
                const maxEmissions = Math.max(...Object.values(branchEmissionsData));
                const scaleFactor = maxEmissions / Math.max(...Object.values(emissionsPerEmployeeData)) * 0.5;
                const scaledPerEmployeeData = {};
                
                Object.keys(emissionsPerEmployeeData).forEach(key => {
                    scaledPerEmployeeData[key] = emissionsPerEmployeeData[key] * scaleFactor;
                });
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.values(branchNames),
                        datasets: [
                            {
                                label: 'Total Emissions',
                                data: Object.values(branchEmissionsData),
                                backgroundColor: '#3498db', // Blue
                                borderRadius: 4
                            },
                            {
                                label: 'Emissions Per Employee (scaled)',
                                data: Object.values(scaledPerEmployeeData),
                                backgroundColor: '#9b59b6', // Purple
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        ...barOptions,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Emissions (kg CO2)'
                                }
                            }
                        }
                    }
                });
            }
        }
        </script>
</body>
</html>
<?php
// End output buffering and send all output
ob_end_flush();
?>