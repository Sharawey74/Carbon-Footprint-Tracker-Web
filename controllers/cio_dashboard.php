<?php
/**
 * CIO Dashboard controller for handling CIO dashboard views
 */

// Check if direct access
if (!defined('ROOT_PATH')) {
    die('Direct access not permitted');
}

// Include required files
use Services\BranchService;
use Services\CarbonFootprintService;
use Services\ReductionStrategyService;
use Services\ReportGenerationService;
use Services\AuditLoggingService;
use Models\CarbonFootprintMetrics;
use Models\ReductionStrategy;
use Exceptions\DataAccessException;
use Exceptions\ReportGenerationException;

/**
 * Display CIO dashboard
 */
function dashboard() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CIO') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    try {
        // Get services from container
        $branchService = $container->get(BranchService::class);
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        $reductionStrategyService = $container->get(ReductionStrategyService::class);
        
        // Define city map
        $cityMap = [
        'Cairo' => 1,
        'Alexandria' => 2,
        'Giza' => 3,
        'Aswan' => 4
    ];

        // Define status map for reduction plans
        $statusMap = [
        1 => 'Pending',
            2 => 'Approved',
            3 => 'In Progress',
            4 => 'Completed',
            5 => 'Rejected'
        ];
        
        // Get data for dashboard
        $emissionData = loadEmissionData($branchService, $carbonFootprintService);
        $reductionPlans = $reductionStrategyService->getAllStrategies();
        
        // Log audit action
        logAuditAction($_SESSION['user_id'], 'VIEW', 'Dashboard', null);
        
        // Display dashboard
        $pageTitle = 'CIO Dashboard';
        include VIEWS_PATH . '/dashboard/cio.php';
    } catch (Exception $e) {
        error_log("CIO Dashboard error: " . $e->getMessage());
        $_SESSION['flash_message'] = 'Error loading dashboard data';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/');
        exit;
    }
}

/**
 * Handle saving status of reduction plans
 */
function saveStatus() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CIO') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Get service from container
            $reductionStrategyService = $container->get(ReductionStrategyService::class);
            
            // Update status of each plan
            if (isset($_POST['status']) && is_array($_POST['status'])) {
                foreach ($_POST['status'] as $planId => $statusId) {
                    $reductionStrategyService->updatePlanStatus($planId, (int)$statusId);
                    
                    // Log audit action
                    logAuditAction($_SESSION['user_id'], 'UPDATE', 'ReductionStrategy', $planId);
                }
                
                $_SESSION['flash_message'] = 'Plan statuses updated successfully';
                $_SESSION['flash_type'] = 'success';
            }
        } catch (Exception $e) {
            error_log("Status update error: " . $e->getMessage());
            $_SESSION['flash_message'] = 'Error updating plan statuses';
            $_SESSION['flash_type'] = 'danger';
        }
    }
    
    // Redirect back to dashboard
    header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
    exit;
}

/**
 * Generate a city report
 */
function generateCityReport() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CIO') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    // Get city from query parameters
    $city = $_GET['city'] ?? null;
    if (!$city) {
        $_SESSION['flash_message'] = 'City parameter is required';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    }
    
    try {
        // Get services from container
        $branchService = $container->get(BranchService::class);
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        $reportService = $container->get(ReportGenerationService::class);
        
        // Get data for the city
        $emissionData = loadEmissionData($branchService, $carbonFootprintService);
        $cityData = $emissionData[$city] ?? [];
        
        // Log audit action
        logAuditAction($_SESSION['user_id'], 'REPORT', 'City', $city);
        
        // Generate report
        $reportService->generateCityReport($city, $cityData);
        
        // No need to redirect as the report will be directly output
    } catch (ReportGenerationException $e) {
        error_log("Report generation error: " . $e->getMessage());
        $_SESSION['flash_message'] = 'Error generating city report';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        $_SESSION['flash_message'] = 'An unexpected error occurred';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    }
}

/**
 * Generate a report for a specific reduction plan
 */
function generateReductionPlanReport() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CIO') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    // Enable error display for debugging
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    // Get plan ID from query parameters
    $planId = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 0;
    error_log("Generating PDF for plan ID: " . $planId);
    
    if (!$planId) {
        $_SESSION['flash_message'] = 'Reduction plan ID is required';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    }
    
    try {
        // Get services from container
        error_log("Getting services from container");
        $reductionStrategyService = $container->get(ReductionStrategyService::class);
        $reportService = $container->get(ReportGenerationService::class);
        
        // Get the reduction plan
        error_log("Fetching plan with ID: " . $planId);
        $plan = $reductionStrategyService->getStrategyById($planId);
        error_log("Retrieved plan: " . ($plan ? "Success" : "Failed"));
        
        if (!$plan) {
            throw new Exception('Reduction plan not found for ID: ' . $planId);
        }
        
        // Log plan details for debugging
        error_log("Plan details: ID=" . $plan->getReductionId() . 
                 ", Branch=" . $plan->getBranchId() . 
                 ", Strategy=" . $plan->getStrategy() . 
                 ", Status=" . $plan->getStatusId());
        
        // Log audit action
        logAuditAction($_SESSION['user_id'], 'REPORT', 'ReductionStrategy', $planId);
        
        // Generate report
        error_log("Generating report for plan ID: " . $planId);
        $reportService->generateReductionPlanReport($plan);
        
        // No need to redirect as the report will be directly output
    } catch (ReportGenerationException $e) {
        error_log("Report generation error: " . $e->getMessage() . ", Trace: " . $e->getTraceAsString());
        $_SESSION['flash_message'] = 'Error generating reduction plan report: ' . $e->getMessage();
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage() . ", Trace: " . $e->getTraceAsString());
        $_SESSION['flash_message'] = 'An unexpected error occurred: ' . $e->getMessage();
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    }
}

/**
 * Generate a comparative report for multiple branches in a city
 */
function generateComparativeReport() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CIO') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    // Get city from query parameters
    $city = $_GET['city'] ?? null;
    if (!$city) {
        $_SESSION['flash_message'] = 'City parameter is required';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    }
    
    try {
        // Get services from container
        $branchService = $container->get(BranchService::class);
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        $reportService = $container->get(ReportGenerationService::class);
        
        // Get city ID
        $cityMap = [
        'Cairo' => 1,
        'Alexandria' => 2,
        'Giza' => 3,
        'Aswan' => 4
    ];
        $cityId = $cityMap[$city] ?? 0;
        
        if (!$cityId) {
            throw new Exception('Invalid city name');
        }
        
        // Get branches in the city
        $branches = $branchService->getBranchesByCity($cityId);
        if (empty($branches)) {
            throw new Exception('No branches found for this city');
        }
        
        // Get metrics for each branch
        $metricsList = [];
        foreach ($branches as $branch) {
            $metrics = $carbonFootprintService->getCarbonFootprintMetrics($branch->getBranchId());
            $metricsList[] = $metrics;
        }
        
        // Log audit action
        logAuditAction($_SESSION['user_id'], 'REPORT', 'ComparativeCity', $city);
        
        // Generate report
        $reportService->generateComparativeReport($metricsList);
        
        // No need to redirect as the report will be directly output
    } catch (ReportGenerationException $e) {
        error_log("Report generation error: " . $e->getMessage());
        $_SESSION['flash_message'] = 'Error generating comparative report';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        $_SESSION['flash_message'] = 'An unexpected error occurred';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    }
}

/**
 * Handle updating a reduction strategy status
 */
function updateReductionPlanStatus() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CIO') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Get service from container
            $reductionStrategyService = $container->get(ReductionStrategyService::class);
            
            // Get plan ID and new status
            $planId = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0;
            $statusId = isset($_POST['status_id']) ? (int)$_POST['status_id'] : 0;
            
            if (!$planId || !$statusId) {
                throw new Exception('Plan ID and status ID are required');
            }
            
            // Update plan status
            $reductionStrategyService->updatePlanStatus($planId, $statusId);
            
            // Log audit action
            logAuditAction($_SESSION['user_id'], 'UPDATE', 'ReductionStrategy', $planId);
            
            $_SESSION['flash_message'] = 'Plan status updated successfully';
            $_SESSION['flash_type'] = 'success';
        } catch (Exception $e) {
            error_log("Status update error: " . $e->getMessage());
            $_SESSION['flash_message'] = 'Error updating plan status: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
        }
    }
    
    // Redirect back to dashboard
    header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
    exit;
}

/**
 * Generate emissions report for Cairo
 */
function generate_cairo_report() {
    generateCityEmissionsReport('Cairo');
}

/**
 * Generate emissions report for Alexandria
 */
function generate_alexandria_report() {
    generateCityEmissionsReport('Alexandria');
}

/**
 * Generate emissions report for Giza
 */
function generate_giza_report() {
    generateCityEmissionsReport('Giza');
}

/**
 * Generate emissions report for Aswan
 */
function generate_aswan_report() {
    generateCityEmissionsReport('Aswan');
}

/**
 * Alias for generateReductionPlanReport to maintain URL convention compatibility
 */
function generate_reduction_plan_report() {
    return generateReductionPlanReport();
}

/**
 * Debug function to directly test PDF generation
 */
function debug_pdf() {
    global $container;
    
    // Enable error display for debugging
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    // Get plan ID from query parameters (default to 1 if not specified)
    $planId = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 1;
    
    try {
        // Get services from container
        $reductionStrategyService = $container->get(ReductionStrategyService::class);
        $reportService = $container->get(ReportGenerationService::class);
        
        // Get the reduction plan
        $plan = $reductionStrategyService->getStrategyById($planId);
        
        if (!$plan) {
            echo "Reduction plan not found for ID: " . $planId;
            exit;
        }
        
        echo "Found plan with ID: " . $plan->getReductionId() . "<br>";
        echo "Branch ID: " . $plan->getBranchId() . "<br>";
        echo "Strategy: " . $plan->getStrategy() . "<br>";
        echo "Status ID: " . $plan->getStatusId() . "<br>";
        echo "Implementation Costs: $" . number_format($plan->getImplementationCosts(), 2) . "<br>";
        echo "Projected Annual Profits: $" . number_format($plan->getProjectedAnnualProfits(), 2) . "<br>";
        
        echo "<p>Click to <a href='" . APP_URL . "/?controller=cio&action=debug_pdf_generate&plan_id=" . $planId . "' target='_blank'>Generate PDF</a></p>";
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

/**
 * Generate PDF for direct testing
 */
function debug_pdf_generate() {
    global $container;
    
    // Enable error display for debugging
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    // Get plan ID from query parameters
    $planId = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 1;
    
    try {
        // Get services from container
        $reductionStrategyService = $container->get(ReductionStrategyService::class);
        $reportService = $container->get(ReportGenerationService::class);
        
        // Get the reduction plan
        $plan = $reductionStrategyService->getStrategyById($planId);
        
        if (!$plan) {
            echo "Reduction plan not found for ID: " . $planId;
            exit;
        }
        
        // Generate report
        $reportService->generateReductionPlanReport($plan);
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

/**
 * Helper function to generate city emissions report
 * 
 * @param string $cityName The name of the city
 */
function generateCityEmissionsReport($cityName) {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'CIO') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    try {
        // Get services from container
        $branchService = $container->get(BranchService::class);
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        $reportService = $container->get(ReportGenerationService::class);
        
        // Get city ID
        $cityMap = [
            'Cairo' => 1,
            'Alexandria' => 2,
            'Giza' => 3,
            'Aswan' => 4
        ];
        $cityId = $cityMap[$cityName] ?? 0;
        
        if (!$cityId) {
            throw new Exception('Invalid city name');
        }
        
        // Get branches in the city
        $branches = $branchService->getBranchesByCity($cityId);
        if (empty($branches)) {
            throw new Exception('No branches found for this city');
        }
        
        // Get data for the city
        $cityData = [];
        foreach ($branches as $branch) {
            $branchId = $branch->getBranchId();
            $metrics = $carbonFootprintService->getCarbonFootprintMetrics($branchId);
            
            $cityData[] = [
                'branch_id' => $branchId,
                'branch_name' => $branch->getLocation(),
                'production' => $metrics->getProductionEmissions(),
                'packaging' => $metrics->getPackagingEmissions(),
                'distribution' => $metrics->getDistributionEmissions(),
                'total' => $metrics->getTotalEmissions()
            ];
        }
        
        // Log audit action
        logAuditAction($_SESSION['user_id'], 'REPORT', 'City', $cityId);
        
        // Generate report - pass cityName and cityData
        $reportService->generateCityReport($cityName, $cityData);
        
        // No need to redirect as the report will be directly output
    } catch (ReportGenerationException $e) {
        error_log("Report generation error: " . $e->getMessage());
        $_SESSION['flash_message'] = 'Error generating city report: ' . $e->getMessage();
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        $_SESSION['flash_message'] = 'An unexpected error occurred: ' . $e->getMessage();
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/?controller=cio&action=dashboard');
        exit;
    }
}

/**
 * Helper function to load emission data
 */
function loadEmissionData($branchService, $carbonFootprintService) {
    $data = [];
    $cityMap = [
        'Cairo' => 1,
        'Alexandria' => 2,
        'Giza' => 3,
        'Aswan' => 4
    ];
    
    try {
        // Get all branches
        $branches = $branchService->getAllBranches();
        
        // Group branches by city and calculate emissions
        foreach ($cityMap as $cityName => $cityId) {
            $cityBranches = array_filter($branches, function($branch) use ($cityId) {
                return $branch->getCityId() == $cityId;
            });
            
            $data[$cityName] = getCityEmissions($cityBranches, $carbonFootprintService);
        }
    } catch (Exception $e) {
        error_log("Emission data error: " . $e->getMessage());
    }
    
    return $data;
}

/**
 * Helper function to get emissions for branches in a city
 */
function getCityEmissions($branches, $carbonFootprintService) {
    $emissions = [];
    
    foreach ($branches as $branch) {
        $branchId = $branch->getBranchId();
        $metrics = $carbonFootprintService->getCarbonFootprintMetrics($branchId);
        
        $emissions[] = [
            'branch_id' => $branchId,
            'branch_name' => $branch->getLocation(),
            'production' => $metrics->getProductionEmissions(),
            'packaging' => $metrics->getPackagingEmissions(),
            'distribution' => $metrics->getDistributionEmissions(),
            'total' => $metrics->getTotalEmissions()
        ];
    }
    
    return $emissions;
}

/**
 * Helper function to log audit actions
 */
function logAuditAction($userID, $action, $tableName, $recordID = null) {
    global $container;
    
    try {
        $auditService = $container->get(AuditLoggingService::class);
        $auditService->logAction($userID, $action, $tableName, $recordID);
    } catch (Exception $e) {
        error_log("Audit logging error: " . $e->getMessage());
    }
}