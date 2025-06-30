<!DOCTYPE html>
<html>
<head>
    <title>CIO Dashboard</title>
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
            display: block !important;
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
        
        /* City tabs styling */
        .city-tabs {
            display: flex;
            background-color: #fff;
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .city-tabs button {
            background-color: #fff;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 500;
            border-right: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            color: #555;
            transition: all 0.3s ease;
        }
        
        .city-tabs button:last-child {
            border-right: none;
        }
        
        .city-tabs button:after {
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
        
        .city-tabs button:hover {
            background-color: #f9f9f9;
            color: var(--primary-color);
        }
        
        .city-tabs button.active {
            background-color: #fff;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .city-tabs button.active:after {
            transform: scaleX(1);
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
        
        /* Summary cards styling - Modernized */
        .summary-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .summary-card {
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
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .summary-card h4 {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0 15px;
            font-size: 16px;
            font-weight: 600;
            color: #555;
        }
        
        .summary-card h4 i {
            margin-right: 8px;
            font-size: 20px;
            color: var(--primary-color);
            height: 40px;
            width: 40px;
            line-height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(46,125,50,0.1), rgba(46,125,50,0.2));
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .summary-card p {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            color: #333;
        }
        
        .summary-card.total {
            border-left: 4px solid var(--danger-color);
            background: linear-gradient(to right, #fff, #fafafa);
        }
        
        .summary-card.total h4 i {
            color: var(--danger-color);
            background: linear-gradient(135deg, rgba(244,67,54,0.1), rgba(244,67,54,0.2));
        }
        
        /* Selected row styling */
        .selected-row {
            background-color: #e3f2fd !important;
            border-left: 4px solid #1890ff;
            box-shadow: 0 2px 8px rgba(24, 144, 255, 0.2);
        }
        
        /* Plan row styling */
        .plan-row {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .plan-row:hover {
            background-color: #f5f5f5;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Status select styling */
        .status-select {
            padding: 8px 12px;
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            background-color: white;
            transition: all 0.3s ease;
            font-size: 14px;
            font-family: 'Poppins', Arial, sans-serif;
        }
        
        .status-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
            outline: none;
        }
        
        /* Action buttons styling */
        .save-btn, .pdf-btn {
            padding: 12px 20px;
            margin-left: 10px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .save-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            box-shadow: 0 2px 5px rgba(76, 175, 80, 0.3);
        }
        
        .save-btn:hover {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.4);
            transform: translateY(-2px);
        }
        
        .pdf-btn {
            background: linear-gradient(135deg, var(--danger-color), #e53935);
            color: white;
            box-shadow: 0 2px 5px rgba(244, 67, 54, 0.3);
        }
        
        .pdf-btn:hover {
            background: linear-gradient(135deg, #e53935, var(--danger-color));
            box-shadow: 0 4px 8px rgba(244, 67, 54, 0.4);
            transform: translateY(-2px);
        }
        
        .pdf-btn:disabled {
            background: linear-gradient(135deg, #cccccc, #bbbbbb);
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }
        
        /* Footer styling */
        .footer {
            background: linear-gradient(135deg, #333, #222);
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
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
            .summary-row {
                flex-direction: column;
            }
            .summary-card {
                min-width: 100%;
            }
            .tabs button {
                padding: 10px;
            }
            .city-tabs {
                flex-wrap: wrap;
            }
            .city-tabs button {
                flex-basis: 50%;
            }
        }
        
        .city-content {
            background-color: white;
            animation: fadeIn 0.5s ease;
            min-height: 250px; /* Ensure there's always visible space for content */
        }
    </style>
    <script>
        // Define APP_URL for use in JavaScript
        const APP_URL = "<?= APP_URL ?>";
        
        // Debug function to check if elements exist
        function checkElements() {
            const elements = [
                { name: 'overview tab', selector: '#overview' },
                { name: 'reduction tab', selector: '#reduction' },
                { name: 'city tabs', selector: '.city-tabs' },
                { name: 'toast container', selector: '.toast-container' }
            ];
            
            elements.forEach(element => {
                const el = document.querySelector(element.selector);
                console.log(`${element.name}: ${el ? 'Found' : 'NOT FOUND'}`);
            });
        }
        
        // Initialize the dashboard functionality when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Debug element existence
            checkElements();
            
            try {
                // Set up tab navigation
                setupTabNavigation();
                
                // Set up city tab navigation
                setupCityTabNavigation();
                
                // Set up plan row selection
                setupPlanRowSelection();
                
                // Set up alert handling
                setupAlerts();
                
                // Show welcome toast message
                showToast('Welcome to the CIO Dashboard', 'info');
                
                // Initialize the first tab on page load
                showTab('overview');
                
                // Initialize the first city tab on page load
                const firstCityTab = document.querySelector('.city-tabs button');
                if (firstCityTab) {
                    const firstCity = firstCityTab.textContent.trim().toLowerCase();
                    showCityTab(firstCity);
                }
            } catch (error) {
                console.error('Error initializing dashboard:', error);
                alert('Error initializing dashboard: ' + error.message);
            }
        });
        
        // Function to handle tab navigation
        function setupTabNavigation() {
            // Get all tab buttons
            const tabButtons = document.querySelectorAll('.tabs button');
            
            // Add click event to each tab button
            tabButtons.forEach((button, index) => {
                button.addEventListener('click', function() {
                    const tabName = index === 0 ? 'overview' : 'reduction';
                    showTab(tabName);
                });
            });
        }
        
        // Function to show a specific tab
        function showTab(tabName) {
            console.log('Showing tab:', tabName); // Debug logging
            
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
                tab.style.display = 'none';
            });
            
            // Show selected tab with animation
            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.classList.add('active');
                selectedTab.style.display = 'block';
                
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
            } else {
                console.error('Tab not found:', tabName);
            }
        }
        
        // Function to handle city tab navigation
        function setupCityTabNavigation() {
            // Get all city tab buttons
            const cityTabButtons = document.querySelectorAll('.city-tabs button');
            
            // Add click event to each city tab button
            cityTabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const cityName = this.textContent.trim().toLowerCase();
                    showCityTab(cityName);
                });
            });
        }
        
        // Function to show a specific city tab
        function showCityTab(cityName) {
            console.log('Showing city tab:', cityName); // Debug logging
            
            // Hide all city contents
            document.querySelectorAll('.city-content').forEach(content => {
                content.style.display = 'none';
            });
            
            // Show selected city content with animation
            const contentId = cityName + '-content';
            const selectedContent = document.getElementById(contentId);
            
            if (selectedContent) {
                selectedContent.style.display = 'block';
                
                // Add fade-in animation
                selectedContent.style.animation = 'none';
                setTimeout(() => {
                    selectedContent.style.animation = 'fadeIn 0.5s ease';
                }, 10);
                
                // Update active city tab button
                document.querySelectorAll('.city-tabs button').forEach(button => {
                    button.classList.remove('active');
                });
                
                // Find the button that was clicked and add active class
                document.querySelectorAll('.city-tabs button').forEach(button => {
                    if (button.textContent.trim().toLowerCase() === cityName) {
                        button.classList.add('active');
                    }
                });
                
                // Show a toast notification
                showToast(`Viewing ${cityName.charAt(0).toUpperCase() + cityName.slice(1)} emissions data`, 'info');
            } else {
                console.error('City content not found:', contentId);
            }
        }
        
        // Function to handle plan row selection
        function setupPlanRowSelection() {
            // Get all plan rows
            const planRows = document.querySelectorAll('.plan-row');
            
            // Add click event to each plan row
            planRows.forEach(row => {
                row.addEventListener('click', function() {
                    // Remove selected class from all rows
                    document.querySelectorAll('.plan-row').forEach(r => {
                        r.classList.remove('selected-row');
                    });
                    
                    // Add selected class to clicked row
                    this.classList.add('selected-row');
                    
                    // Add animation to selected row
                    this.style.animation = 'none';
                    setTimeout(() => {
                        this.style.animation = 'pulse-highlight 1s ease';
                    }, 10);
                    
                    // Enable the generate PDF button
                    const generatePdfBtn = document.getElementById('generatePdfBtn');
                    generatePdfBtn.disabled = false;
                    
                    // Show toast notification
                    const strategy = this.children[2].textContent.trim();
                    const planId = this.getAttribute('data-plan-id');
                    showToast(`Selected plan: ${strategy} (ID: ${planId})`, 'info');
                });
            });
            
            // Add event listener to generate PDF button
            const generatePdfBtn = document.getElementById('generatePdfBtn');
            generatePdfBtn.addEventListener('click', function() {
                // Get selected plan ID
                const selectedRow = document.querySelector('.plan-row.selected-row');
                if (selectedRow) {
                    const planId = selectedRow.getAttribute('data-plan-id');
                    if (planId) {
                        showToast(`Generating PDF for plan ID: ${planId}`, 'success');
                        window.open(`${APP_URL}/?controller=cio&action=generate_reduction_plan_report&plan_id=${planId}`, '_blank');
                    } else {
                        showToast('No plan selected. Please click on a plan first.', 'error');
                    }
                } else {
                    showToast('No plan selected. Please click on a plan first.', 'error');
                }
            });
        }
        
        // Function to handle alerts
        function setupAlerts() {
            // Get all alert elements
            const alertElements = document.querySelectorAll('.alert');
            
            // Add close event to each alert
            alertElements.forEach(alert => {
                const closeBtn = alert.querySelector('.close-btn');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        alert.style.opacity = 0;
                        setTimeout(() => {
                            alert.remove();
                        }, 500);
                    });
                }
                
                // Auto-dismiss alerts after 8 seconds
                setTimeout(() => {
                    alert.style.opacity = 0;
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 8000);
            });
        }
        
        // Function to show a toast notification
        function showToast(message, type) {
            // Create toast container if it doesn't exist
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container';
                document.body.appendChild(toastContainer);
            }
            
            // Set icon based on type
            let icon = 'info-circle';
            if (type === 'success') icon = 'check-circle';
            if (type === 'error') icon = 'exclamation-circle';
            if (type === 'warning') icon = 'exclamation-triangle';
            
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <span class="toast-icon"><i class="fas fa-${icon}"></i></span>
                <span class="toast-message">${message}</span>
            `;
            
            // Add toast to container
            toastContainer.appendChild(toast);
            
            // Remove toast after 3 seconds
            setTimeout(() => {
                toast.style.opacity = 0;
                setTimeout(() => {
                    toast.remove();
                }, 500);
            }, 3000);
        }
        
        // Add animation for row highlighting
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                @keyframes pulse-highlight {
                    0% { background-color: rgba(76, 175, 80, 0.2); }
                    50% { background-color: rgba(76, 175, 80, 0.4); }
                    100% { background-color: rgba(0, 0, 0, 0); }
                }
                
                .text-danger { color: var(--danger-color); }
                .text-warning { color: var(--warning-color); }
                .text-success { color: var(--success-color); }
            </style>
        `);
    </script>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="header-logo">
            <img src="<?= APP_URL ?>/img/coffee_eco_logo.svg" alt="Logo" width="50" height="50" class="animate__animated animate__fadeIn">
            <div class="header-title">
                <h1><i class="fas fa-leaf"></i> Coffee Factory Carbon Tracker</h1>
                <h2>Chief Information Officer Dashboard</h2>
            </div>
        </div>
        <div class="user-info animate__animated animate__fadeIn">
            <p><i class="fas fa-user-circle"></i> <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User' ?></p>
            <p><i class="fas fa-user-shield"></i> Role: CIO</p>
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
            <button class="active"><i class="fas fa-globe-americas"></i> Overview</button>
            <button><i class="fas fa-leaf"></i> Reduction Plans</button>
        </div>

        <!-- Overview Tab -->
        <div id="overview" class="tab-content active" style="display: block;">
            <h2>Carbon Emissions Overview</h2>
            
            <!-- City Tabs -->
            <div class="city-tabs">
                <?php foreach (array_keys($cityMap) as $index => $city): ?>
                    <button class="<?= $index === 0 ? 'active' : '' ?>">
                        <?= $city ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <!-- City Tab Contents -->
            <?php foreach (array_keys($cityMap) as $index => $city): ?>
                <?php 
                $cityData = $emissionData[$city] ?? [];
                $totalProduction = array_sum(array_column($cityData, 'production'));
                $totalPackaging = array_sum(array_column($cityData, 'packaging'));
                $totalDistribution = array_sum(array_column($cityData, 'distribution'));
                $totalEmissions = array_sum(array_column($cityData, 'total'));
                $display = $index === 0 ? 'block' : 'none';
                ?>
                <div id="<?= strtolower($city) ?>-content" class="city-content" style="display: <?= $display ?>;">
                    <!-- Summary Cards -->
                    <div class="summary-row">
                        <div class="summary-card">
                            <h4><i class="fas fa-industry"></i> Production</h4>
                            <p><?= number_format($totalProduction, 2) ?> kg</p>
                        </div>
                        <div class="summary-card">
                            <h4><i class="fas fa-box"></i> Packaging</h4>
                            <p><?= number_format($totalPackaging, 2) ?> kg</p>
                        </div>
                        <div class="summary-card">
                            <h4><i class="fas fa-truck"></i> Distribution</h4>
                            <p><?= number_format($totalDistribution, 2) ?> kg</p>
                        </div>
                        <div class="summary-card total">
                            <h4><i class="fas fa-globe"></i> Total Emissions</h4>
                            <p><?= number_format($totalEmissions, 2) ?> kg</p>
                        </div>
                    </div>
                    
                    <!-- Data Table -->
                    <table>
                        <tr>
                            <th>Branch ID</th>
                            <th>Branch Name</th>
                            <th>Production (kg)</th>
                            <th>Packaging (kg)</th>
                            <th>Distribution (kg)</th>
                            <th>Total (kg)</th>
                        </tr>
                        <?php if (!empty($cityData)): ?>
                            <?php foreach ($cityData as $row): ?>
                                <tr>
                                    <td><?= $row['branch_id'] ?></td>
                                    <td><?= $row['branch_name'] ?></td>
                                    <td><?= number_format($row['production'], 2) ?></td>
                                    <td><?= number_format($row['packaging'], 2) ?></td>
                                    <td><?= number_format($row['distribution'], 2) ?></td>
                                    <td><strong><?= number_format($row['total'], 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No data available for <?= $city ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                    
                    <!-- Actions -->
                    <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                        <p style="font-weight: 600; font-size: 16px;">Total Emissions: <?= number_format($totalEmissions, 2) ?> kg CO₂</p>
                        <a href="<?= APP_URL ?>/?controller=cio&action=generate_<?= strtolower($city) ?>_report" target="_blank" class="download-link">
                            <i class="fas fa-file-pdf"></i> Generate <?= $city ?> PDF Report
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Reduction Plans Tab -->
        <div id="reduction" class="tab-content" style="display: none;">
            <h2>Carbon Reduction Plans</h2>
            
            <form method="POST" action="<?= APP_URL ?>/?controller=cio&action=saveStatus">
                <table>
                    <tr>
                        <th>Plan ID</th>
                        <th>Branch ID</th>
                        <th>Strategy</th>
                        <th>Implementation Costs</th>
                        <th>Projected Profits</th>
                        <th>ROI</th>
                        <th>Status</th>
                    </tr>
                    <?php if (!empty($reductionPlans)): ?>
                        <?php foreach ($reductionPlans as $plan): ?>
                            <?php 
                                $costs = $plan->getImplementationCosts();
                                $profits = $plan->getProjectedAnnualProfits();
                                $roi = ($costs > 0) ? (($profits - $costs) / $costs) * 100 : 0;
                                $planId = $plan->getReductionId();
                                $roiClass = ($roi < 10) ? 'text-danger' : (($roi < 50) ? 'text-warning' : 'text-success');
                            ?>
                            <tr class="plan-row" data-plan-id="<?= $planId ?>">
                                <td><?= $planId ?></td>
                                <td><?= $plan->getBranchId() ?></td>
                                <td><?= $plan->getStrategy() ?></td>
                                <td>$<?= number_format($costs, 2) ?></td>
                                <td>$<?= number_format($profits, 2) ?></td>
                                <td class="<?= $roiClass ?>"><?= number_format($roi, 1) ?>%</td>
                                <td>
                                    <select name="status[<?= $planId ?>]" class="status-select">
                                        <?php foreach ($statusMap as $id => $name): ?>
                                            <option value="<?= $id ?>" <?= $plan->getStatusId() == $id ? 'selected' : '' ?>>
                                                <?= $name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No reduction plans available</td>
                        </tr>
                    <?php endif; ?>
                </table>
                
                <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" class="save-btn"><i class="fas fa-save"></i> Save Status Changes</button>
                    <button type="button" id="generatePdfBtn" class="pdf-btn" disabled><i class="fas fa-file-pdf"></i> Generate Selected Plan PDF</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Footer Section -->
    <div class="footer">
        <p>&copy; <?= date('Y') ?> CofaktoryCFT - Carbon Footprint Tracker</p>
    </div>
</body>
</html>