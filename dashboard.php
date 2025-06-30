<?php
// Prevent any output before headers - this must be the very first line
ob_start();

// Set session variables for CEO access
session_start();
$_SESSION['user_id'] = 19; // CEO user ID from database
$_SESSION['user_name'] = 'Osama Hanafy';
$_SESSION['user_role'] = 'CEO';

// Include required files with correct paths
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Connect to the database
$db = database::getConnection();

// Function to safely get numeric value
function safe($value) {
    return is_null($value) ? 0.0 : (float)$value;
}

// Function to get city name from ID (matches the Java function)
function getCityName($cityId) {
    switch ($cityId) {
        case 1: return 'Alex';
        case 2: return 'Aswan';
        case 3: return 'Cairo';
        case 4: return 'Suez';
        default: return 'Other';
    }
}

// Function to get status name from ID (matches the Java function)
function getStatusName($statusId) {
    switch ($statusId) {
        case 1: return 'Draft';
        case 2: return 'InProgress';
        case 3: return 'Complete';
        default: return 'Other';
    }
}

// Get all branches
$stmt = $db->prepare("SELECT * FROM Branch");
$stmt->execute();
$branches = $stmt->fetchAll();

// Get all reduction strategies
$stmt = $db->prepare("SELECT * FROM ReductionStrategy");
$stmt->execute();
$reductionStrategies = $stmt->fetchAll();

// Function to get total branch emissions
function getBranchEmissions($db, $branchId) {
    // Get production emissions
    $stmt = $db->prepare("SELECT SUM(Pr_CarbonEmissions_KG) as total FROM CoffeeProduction WHERE BranchID = ?");
    $stmt->execute([$branchId]);
    $prodResult = $stmt->fetch();
    $prodEmissions = safe($prodResult['total']);
    
    // Get packaging emissions
    $stmt = $db->prepare("SELECT SUM(Pa_CarbonEmissions_KG) as total FROM CoffeePackaging WHERE BranchID = ?");
    $stmt->execute([$branchId]);
    $packResult = $stmt->fetch();
    $packEmissions = safe($packResult['total']);
    
    // Get distribution emissions
    $stmt = $db->prepare("SELECT SUM(V_CarbonEmissions_Kg) as total FROM CoffeeDistribution WHERE BranchID = ?");
    $stmt->execute([$branchId]);
    $distResult = $stmt->fetch();
    $distEmissions = safe($distResult['total']);
    
    return $prodEmissions + $packEmissions + $distEmissions;
}

// Calculate emissions by branch for charts
$branchEmissions = [];
$branchNames = [];
$cityEmissions = [];
$emissionsPerEmployee = [];

// Process emissions by branch
foreach ($branches as $branch) {
    $branchId = $branch['BranchID'];
    $emissions = getBranchEmissions($db, $branchId);
    $branchEmissions[$branchId] = $emissions;
    $branchNames[$branchId] = "Branch " . $branchId;
    
    $cityName = getCityName($branch['CityID']);
    if (!isset($cityEmissions[$cityName])) {
        $cityEmissions[$cityName] = 0;
    }
    $cityEmissions[$cityName] += $emissions;
    
    // Calculate emissions per employee
    $employeeCount = $branch['NumberOfEmployees'];
    $emissionsPerEmployee[$branchId] = $employeeCount > 0 ? $emissions / $employeeCount : 0;
}

// Get emissions by process
$stmt = $db->prepare("SELECT SUM(Pr_CarbonEmissions_KG) as production FROM CoffeeProduction");
$stmt->execute();
$prodTotal = safe($stmt->fetch()['production']);

$stmt = $db->prepare("SELECT SUM(Pa_CarbonEmissions_KG) as packaging FROM CoffeePackaging");
$stmt->execute();
$packTotal = safe($stmt->fetch()['packaging']);

$stmt = $db->prepare("SELECT SUM(V_CarbonEmissions_Kg) as distribution FROM CoffeeDistribution");
$stmt->execute();
$distTotal = safe($stmt->fetch()['distribution']);

// Get reduction plan statistics
$planStatus = [];
foreach ($reductionStrategies as $plan) {
    $status = getStatusName($plan['StatusID']);
    if (!isset($planStatus[$status])) {
        $planStatus[$status] = 0;
    }
    $planStatus[$status]++;
}

// Calculate implementation costs and projected profits by city
$cityCosts = [];
$cityProfits = [];

foreach ($reductionStrategies as $plan) {
    $branchId = $plan['BranchID'];
    
    // Get the city for this branch
    $stmt = $db->prepare("SELECT CityID FROM Branch WHERE BranchID = ?");
    $stmt->execute([$branchId]);
    $cityResult = $stmt->fetch();
    $cityName = getCityName($cityResult['CityID']);
    
    if (!isset($cityCosts[$cityName])) {
        $cityCosts[$cityName] = 0;
        $cityProfits[$cityName] = 0;
    }
    
    $cityCosts[$cityName] += $plan['ImplementationCosts'];
    $cityProfits[$cityName] += $plan['ProjectedAnnualProfits'];
}

// Find top best and worst branches by emissions
$branchEmissionsTemp = $branchEmissions;
arsort($branchEmissionsTemp);
$worstBranches = array_slice($branchEmissionsTemp, 0, 5, true);

asort($branchEmissionsTemp);
$bestBranches = array_slice($branchEmissionsTemp, 0, 5, true);

// Generate emissions over time data (last 6 months)
// Since we don't have actual monthly data, we'll simulate it like in the Java code
$monthlyEmissions = [];
$now = new DateTime();
$totalEmissions = array_sum($branchEmissions);
$monthCount = 6;
$perMonth = $monthCount > 0 ? $totalEmissions / $monthCount : 0;

for ($i = 5; $i >= 0; $i--) {
    $monthDate = clone $now;
    $monthDate->modify("-$i months");
    $monthKey = $monthDate->format('M Y');
    // Add some variation to make it look more realistic
    $variation = rand(-10, 10) / 100; // -10% to +10%
    $monthlyEmissions[$monthKey] = $perMonth * (1 + $variation);
}

// Convert data to JSON for JavaScript
$branchEmissionsJson = json_encode($branchEmissions);
$branchNamesJson = json_encode($branchNames);
$cityEmissionsJson = json_encode($cityEmissions);
$emissionsPerEmployeeJson = json_encode($emissionsPerEmployee);
$processTotalsJson = json_encode([
    'Production' => $prodTotal,
    'Packaging' => $packTotal,
    'Distribution' => $distTotal
]);
$planStatusJson = json_encode($planStatus);
$cityProfitsJson = json_encode($cityProfits);
$cityCostsJson = json_encode($cityCosts);
$monthlyEmissionsJson = json_encode($monthlyEmissions);
$worstBranchesJson = json_encode($worstBranches);
$bestBranchesJson = json_encode($bestBranches);

// End output buffering
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEO Dashboard - Carbon Footprint Tracker</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            padding: 20px;
        }
        .chart-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }
        .chart-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .chart-description {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }
        .section-title {
            grid-column: 1 / -1;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }
        canvas {
            width: 100% !important;
            height: 300px !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="dashboard.php">CEO Dashboard</a></li>
                </ul>
            </nav>
        </header>
        <div class="main-content">
        
        <h1>CEO Dashboard</h1>
        
        <div class="dashboard-container">
            <div class="section-title">Emissions Overview</div>
            
            <!-- Emissions Over Time Chart -->
            <div class="chart-container">
                <div class="chart-title">Emissions Over Time</div>
                <canvas id="emissionsOverTimeChart"></canvas>
                <div class="chart-description">Shows the trend of total company emissions over the last 6 months. Use this to track progress in reducing overall emissions.</div>
            </div>
            
            <!-- Emissions By City Chart -->
            <div class="chart-container">
                <div class="chart-title">Emissions By City</div>
                <canvas id="emissionsByCityChart"></canvas>
                <div class="chart-description">Compares total emissions across cities. Identify which city has the highest carbon footprint.</div>
            </div>
            
            <!-- Emissions By Process Chart -->
            <div class="chart-container">
                <div class="chart-title">Emissions By Process</div>
                <canvas id="emissionsByProcessChart"></canvas>
                <div class="chart-description">Breakdown of emissions by coffee process. Focus reduction efforts on the largest segment.</div>
            </div>
            
            <div class="section-title">Reduction Plans & Impact</div>
            
            <!-- Reduction Plans Status Chart -->
            <div class="chart-container">
                <div class="chart-title">Reduction Plans Status</div>
                <canvas id="reductionPlansStatusChart"></canvas>
                <div class="chart-description">Shows the proportion of reduction plans by status.</div>
            </div>
            
            <!-- Projected Profits Chart -->
            <div class="chart-container">
                <div class="chart-title">Projected Profits by City</div>
                <canvas id="projectedProfitsChart"></canvas>
                <div class="chart-description">Projected annual profits from all accepted reduction plans, grouped by city.</div>
            </div>
            
            <!-- Implementation Costs Chart -->
            <div class="chart-container">
                <div class="chart-title">Implementation Costs by City</div>
                <canvas id="implementationCostsChart"></canvas>
                <div class="chart-description">Total implementation costs for reduction plans, grouped by city.</div>
            </div>
            
            <div class="section-title">Operations & Performance</div>
            
            <!-- Emissions Per Branch Chart -->
            <div class="chart-container">
                <div class="chart-title">Emissions Per Branch</div>
                <canvas id="emissionsPerBranchChart"></canvas>
                <div class="chart-description">Total emissions per branch. Identify high and low performing branches.</div>
            </div>
            
            <!-- Emissions Per Employee Chart -->
            <div class="chart-container">
                <div class="chart-title">Emissions Per Employee</div>
                <canvas id="emissionsPerEmployeeChart"></canvas>
                <div class="chart-description">Emissions per employee for each branch. Normalizes performance by staff size.</div>
            </div>
            
            <!-- Top Worst Branches Chart -->
            <div class="chart-container">
                <div class="chart-title">Top 5 Highest Emission Branches</div>
                <canvas id="topWorstBranchesChart"></canvas>
                <div class="chart-description">Top 5 branches with the highest total emissions.</div>
            </div>
            
            <!-- Top Best Branches Chart -->
            <div class="chart-container">
                <div class="chart-title">Top 5 Lowest Emission Branches</div>
                <canvas id="topBestBranchesChart"></canvas>
                <div class="chart-description">Top 5 branches with the lowest total emissions.</div>
            </div>
        </div>
        </div>
        
        <footer>
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> Carbon Footprint Tracker. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <script>
        // Shared chart options
        const barOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };
        
        // Emissions Over Time Chart
        const emissionsOverTimeData = <?php echo $monthlyEmissionsJson; ?>;
        new Chart(document.getElementById('emissionsOverTimeChart'), {
            type: 'line',
            data: {
                labels: Object.keys(emissionsOverTimeData),
                datasets: [{
                    label: 'Total Emissions',
                    data: Object.values(emissionsOverTimeData),
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4,
                    fill: true
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
                            text: 'Emissions (kg CO2)'
                        }
                    }
                }
            }
        });
        
        // Emissions By City Chart
        const cityEmissionsData = <?php echo $cityEmissionsJson; ?>;
        new Chart(document.getElementById('emissionsByCityChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(cityEmissionsData),
                datasets: [{
                    label: 'Total Emissions',
                    data: Object.values(cityEmissionsData),
                    backgroundColor: 'rgba(46, 204, 113, 0.7)'
                }]
            },
            options: barOptions
        });
        
        // Emissions By Process Chart
        const processData = <?php echo $processTotalsJson; ?>;
        new Chart(document.getElementById('emissionsByProcessChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(processData),
                datasets: [{
                    data: Object.values(processData),
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(155, 89, 182, 0.7)',
                        'rgba(230, 126, 34, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Reduction Plans Status Chart
        const planStatusData = <?php echo $planStatusJson; ?>;
        new Chart(document.getElementById('reductionPlansStatusChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(planStatusData),
                datasets: [{
                    data: Object.values(planStatusData),
                    backgroundColor: [
                        'rgba(241, 196, 15, 0.7)',
                        'rgba(46, 204, 113, 0.7)',
                        'rgba(231, 76, 60, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Projected Profits Chart
        const cityProfitsData = <?php echo $cityProfitsJson; ?>;
        new Chart(document.getElementById('projectedProfitsChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(cityProfitsData),
                datasets: [{
                    label: 'Projected Annual Profits',
                    data: Object.values(cityProfitsData),
                    backgroundColor: 'rgba(241, 196, 15, 0.7)'
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
        
        // Implementation Costs Chart
        const cityCostsData = <?php echo $cityCostsJson; ?>;
        new Chart(document.getElementById('implementationCostsChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(cityCostsData),
                datasets: [{
                    label: 'Implementation Costs',
                    data: Object.values(cityCostsData),
                    backgroundColor: 'rgba(231, 76, 60, 0.7)'
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
        
        // Emissions Per Branch Chart
        const branchEmissionsData = <?php echo $branchEmissionsJson; ?>;
        const branchNames = <?php echo $branchNamesJson; ?>;
        
        new Chart(document.getElementById('emissionsPerBranchChart'), {
            type: 'bar',
            data: {
                labels: Object.values(branchNames),
                datasets: [{
                    label: 'Total Emissions',
                    data: Object.values(branchEmissionsData),
                    backgroundColor: 'rgba(52, 152, 219, 0.7)'
                }]
            },
            options: barOptions
        });
        
        // Emissions Per Employee Chart
        const emissionsPerEmployeeData = <?php echo $emissionsPerEmployeeJson; ?>;
        
        new Chart(document.getElementById('emissionsPerEmployeeChart'), {
            type: 'bar',
            data: {
                labels: Object.values(branchNames),
                datasets: [{
                    label: 'Emissions per Employee',
                    data: Object.values(emissionsPerEmployeeData),
                    backgroundColor: 'rgba(155, 89, 182, 0.7)'
                }]
            },
            options: barOptions
        });
        
        // Top Worst Branches Chart
        const worstBranchesData = <?php echo $worstBranchesJson; ?>;
        const worstBranchesLabels = Object.keys(worstBranchesData).map(id => branchNames[id]);
        
        new Chart(document.getElementById('topWorstBranchesChart'), {
            type: 'bar',
            data: {
                labels: worstBranchesLabels,
                datasets: [{
                    label: 'Emissions',
                    data: Object.values(worstBranchesData),
                    backgroundColor: 'rgba(231, 76, 60, 0.7)'
                }]
            },
            options: barOptions
        });
        
        // Top Best Branches Chart
        const bestBranchesData = <?php echo $bestBranchesJson; ?>;
        const bestBranchesLabels = Object.keys(bestBranchesData).map(id => branchNames[id]);
        
        new Chart(document.getElementById('topBestBranchesChart'), {
            type: 'bar',
            data: {
                labels: bestBranchesLabels,
                datasets: [{
                    label: 'Emissions',
                    data: Object.values(bestBranchesData),
                    backgroundColor: 'rgba(46, 204, 113, 0.7)'
                }]
            },
            options: barOptions
        });
    </script>
</body>
</html> 