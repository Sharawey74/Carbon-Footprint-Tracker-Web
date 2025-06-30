<?php
// Prevent any output before headers - this must be the very first line
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

/**
 * Display CEO dashboard
 * This function is called by the routing system in index.php
 */
function dashboard() {
    global $db;
    
    // Check if database connection is available
    if (!$db) {
        $_SESSION['error'] = 'Database connection error. Please check your database settings.';
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
}

    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CEO') {
        $_SESSION['error'] = 'You must be logged in as a CEO to access this page.';
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }

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

    // Get all branches
    $stmt = $db->prepare("SELECT * FROM Branch");
    $stmt->execute();
    $branches = $stmt->fetchAll();

    // Get all reduction strategies
    $stmt = $db->prepare("SELECT * FROM ReductionStrategy");
    $stmt->execute();
    $reductionStrategies = $stmt->fetchAll();

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
    
    // Include the view file
    include VIEWS_PATH . '/dashboard/ceo.php';
}

// Only run this code if not called by the routing system
if (!defined('ROOT_PATH')) {
    // Ensure only CEO can access this page
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'CEO') {
        header('Location: ../index.php');
        exit();
    }
    
    // Call the dashboard function
    dashboard();
}

// End output buffering and send all output
ob_end_flush();
?> 