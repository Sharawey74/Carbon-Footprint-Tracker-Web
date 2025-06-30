<?php
/**
 * Branch Data Entry controller for handling branch user dashboard and data entry
 */

// Check if direct access
if (!defined('ROOT_PATH')) {
    die('Direct access not permitted');
}

// Include required files
use Services\UserService;
use Services\CarbonFootprintService;
use Services\EmissionService;
use Models\CoffeeProduction;
use Models\CoffeePackaging;
use Models\CoffeeDistribution;
use Exceptions\DataAccessException;

/**
 * Display branch user dashboard
 */
function dashboard() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }

    $userId = $_SESSION['user_id'];
    $branchId = $_SESSION['branch_id'];
    
    try {
        // Get services from container
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        
        // Get data for the dashboard
        $metrics = $carbonFootprintService->getCarbonFootprintMetrics($branchId);
        $productionData = $carbonFootprintService->getProductionByBranch($branchId);
        $packagingData = $carbonFootprintService->getPackagingByBranch($branchId);
        $distributionData = $carbonFootprintService->getDistributionByBranch($branchId);

    // Display dashboard
    $pageTitle = 'Branch Dashboard';
    ob_start();
    include VIEWS_PATH . '/branch/dashboard.php';
    $content = ob_get_clean();
    include VIEWS_PATH . '/templates/main_layout.php';
    } catch (\Exception $e) {
        error_log("Dashboard error: " . $e->getMessage());
        $_SESSION['flash_message'] = 'Error loading dashboard data';
        $_SESSION['flash_type'] = 'danger';
        header('Location: ' . APP_URL . '/');
        exit;
    }
}

/**
 * Handle production data submission
 */
function saveProduction() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user_id'];
        $branchId = $_SESSION['branch_id'];
        
        try {
            // Get services from container
            $carbonFootprintService = $container->get(CarbonFootprintService::class);
            $emissionService = $container->get(EmissionService::class);
            
            // Create production object
            $production = new CoffeeProduction(
                0, // ID will be set by database
                $branchId,
                $userId,
                $_POST['supplier'],
                $_POST['coffeeType'],
                $_POST['productType'],
                (float)$_POST['quantity'],
                0, // Emissions will be calculated
                new \DateTime()
            );
            
            // Calculate emissions
            $emissionService->calculateProductionEmissions($production);
            
            // Save to database
            $carbonFootprintService->saveProduction($production);
            
            $_SESSION['flash_message'] = 'Production record added successfully';
            $_SESSION['flash_type'] = 'success';
        } catch (\Exception $e) {
            error_log("Error saving production data: " . $e->getMessage());
            $_SESSION['flash_message'] = 'Error saving production data';
            $_SESSION['flash_type'] = 'danger';
        }
    }
    
    // Redirect back to dashboard
    header('Location: ' . APP_URL . '/?controller=branch&action=dashboard');
    exit;
}

/**
 * Handle packaging data submission
 */
function savePackaging() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user_id'];
        $branchId = $_SESSION['branch_id'];
        
        try {
            // Get services from container
            $carbonFootprintService = $container->get(CarbonFootprintService::class);
            $emissionService = $container->get(EmissionService::class);
            
            // Create packaging object
            $packaging = new CoffeePackaging(
                0, // ID will be set by database
                $branchId,
                $userId,
                (float)$_POST['waste'],
                0, // Emissions will be calculated
                new \DateTime()
            );
            
            // Calculate emissions
            $emissionService->calculatePackagingEmissions($packaging);
            
            // Save to database
            $carbonFootprintService->savePackaging($packaging);
            
            $_SESSION['flash_message'] = 'Packaging record added successfully';
            $_SESSION['flash_type'] = 'success';
        } catch (\Exception $e) {
            error_log("Error saving packaging data: " . $e->getMessage());
            $_SESSION['flash_message'] = 'Error saving packaging data';
            $_SESSION['flash_type'] = 'danger';
        }
    }
    
    // Redirect back to dashboard
    header('Location: ' . APP_URL . '/?controller=branch&action=dashboard');
    exit;
}

/**
 * Handle distribution data submission
 */
function saveDistribution() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user_id'];
        $branchId = $_SESSION['branch_id'];
        
        try {
            // Get services from container
            $carbonFootprintService = $container->get(CarbonFootprintService::class);
            $emissionService = $container->get(EmissionService::class);
            
            // Calculate total distance
            $numVehicles = (int)$_POST['numVehicles'];
            $distancePerVehicle = (float)$_POST['distance'];
            $totalDistance = $numVehicles * $distancePerVehicle;
            
            // Create distribution object
            $distribution = new CoffeeDistribution(
                0, // ID will be set by database
                $branchId,
                $userId,
                $_POST['vehicleType'],
                $numVehicles,
                $distancePerVehicle,
                $totalDistance,
                0, // Fuel efficiency will be calculated
                0, // Emissions will be calculated
                new \DateTime()
            );
            
            // Calculate emissions
            $emissionService->calculateDistributionEmissions($distribution);
            
            // Save to database
            $carbonFootprintService->saveDistribution($distribution);
            
            $_SESSION['flash_message'] = 'Distribution record added successfully';
            $_SESSION['flash_type'] = 'success';
        } catch (\Exception $e) {
            error_log("Error saving distribution data: " . $e->getMessage());
            $_SESSION['flash_message'] = 'Error saving distribution data';
            $_SESSION['flash_type'] = 'danger';
        }
    }
    
    // Redirect back to dashboard
    header('Location: ' . APP_URL . '/?controller=branch&action=dashboard');
    exit;
}

/**
 * Update existing production record
 */
function updateProduction() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user_id'];
        $branchId = $_SESSION['branch_id'];
        
        try {
            // Get services from container
            $carbonFootprintService = $container->get(CarbonFootprintService::class);
            $emissionService = $container->get(EmissionService::class);
            
            // Get production ID from form
            $productionId = isset($_POST['production_id']) ? (int)$_POST['production_id'] : 0;
            
            // Create production object
            $production = new CoffeeProduction(
                $productionId,
                $branchId,
                $userId,
                $_POST['supplier'],
                $_POST['coffeeType'],
                $_POST['productType'],
                (float)$_POST['quantity'],
                0, // Emissions will be calculated
                new \DateTime()
            );
            
            // Calculate emissions
            $emissionService->calculateProductionEmissions($production);
            
            // Save to database
            if ($productionId > 0) {
                $carbonFootprintService->updateProduction($production);
                $_SESSION['flash_message'] = 'Production record updated successfully';
            } else {
                $carbonFootprintService->saveProduction($production);
                $_SESSION['flash_message'] = 'Production record added successfully';
            }
            $_SESSION['flash_type'] = 'success';
        } catch (\Exception $e) {
            error_log("Error updating production data: " . $e->getMessage());
            $_SESSION['flash_message'] = 'Error updating production data: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
        }
    }
    
    // Redirect back to dashboard
    header('Location: ' . APP_URL . '/?controller=branch&action=dashboard');
    exit;
}

/**
 * Update existing packaging record
 */
function updatePackaging() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user_id'];
        $branchId = $_SESSION['branch_id'];
        
        try {
            // Get services from container
            $carbonFootprintService = $container->get(CarbonFootprintService::class);
            $emissionService = $container->get(EmissionService::class);
            
            // Get packaging ID from form
            $packagingId = isset($_POST['packaging_id']) ? (int)$_POST['packaging_id'] : 0;
            
            // Create packaging object
            $packaging = new CoffeePackaging(
                $packagingId,
                $branchId,
                $userId,
                (float)$_POST['waste'],
                0, // Emissions will be calculated
                new \DateTime()
            );
            
            // Calculate emissions
            $emissionService->calculatePackagingEmissions($packaging);
            
            // Save to database
            if ($packagingId > 0) {
                $carbonFootprintService->updatePackaging($packaging);
                $_SESSION['flash_message'] = 'Packaging record updated successfully';
            } else {
                $carbonFootprintService->savePackaging($packaging);
                $_SESSION['flash_message'] = 'Packaging record added successfully';
            }
            $_SESSION['flash_type'] = 'success';
        } catch (\Exception $e) {
            error_log("Error updating packaging data: " . $e->getMessage());
            $_SESSION['flash_message'] = 'Error updating packaging data: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
        }
    }
    
    // Redirect back to dashboard
    header('Location: ' . APP_URL . '/?controller=branch&action=dashboard');
    exit;
}

/**
 * Update existing distribution record
 */
function updateDistribution() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_SESSION['user_id'];
        $branchId = $_SESSION['branch_id'];
        
        try {
            // Get services from container
            $carbonFootprintService = $container->get(CarbonFootprintService::class);
            $emissionService = $container->get(EmissionService::class);
            
            // Get distribution ID from form
            $distributionId = isset($_POST['distribution_id']) ? (int)$_POST['distribution_id'] : 0;
            
            // Calculate total distance
            $numVehicles = (int)$_POST['numVehicles'];
            $distancePerVehicle = (float)$_POST['distance'];
            $totalDistance = $numVehicles * $distancePerVehicle;
            
            // Create distribution object
            $distribution = new CoffeeDistribution(
                $distributionId,
                $branchId,
                $userId,
                $_POST['vehicleType'],
                $numVehicles,
                $distancePerVehicle,
                $totalDistance,
                0, // Fuel efficiency will be calculated
                0, // Emissions will be calculated
                new \DateTime()
            );
            
            // Calculate emissions
            $emissionService->calculateDistributionEmissions($distribution);
            
            // Save to database
            if ($distributionId > 0) {
                $carbonFootprintService->updateDistribution($distribution);
                $_SESSION['flash_message'] = 'Distribution record updated successfully';
            } else {
                $carbonFootprintService->saveDistribution($distribution);
                $_SESSION['flash_message'] = 'Distribution record added successfully';
            }
            $_SESSION['flash_type'] = 'success';
        } catch (\Exception $e) {
            error_log("Error updating distribution data: " . $e->getMessage());
            $_SESSION['flash_message'] = 'Error updating distribution data: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
        }
    }
    
    // Redirect back to dashboard
    header('Location: ' . APP_URL . '/?controller=branch&action=dashboard');
    exit;
}

/**
 * Refresh production data
 */
function refreshProduction() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $branchId = $_SESSION['branch_id'];
    
    try {
        // Get services from container
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        
        // Get production data for the branch and user
        $productionData = $carbonFootprintService->getProductionByBranchAndUser($branchId, $userId);
        
        // Return JSON response with production data
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $productionData
        ]);
        exit;
    } catch (\Exception $e) {
        error_log("Error refreshing production data: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error refreshing production data: ' . $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Refresh packaging data
 */
function refreshPackaging() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $branchId = $_SESSION['branch_id'];
    
    try {
        // Get services from container
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        
        // Get packaging data for the branch and user
        $packagingData = $carbonFootprintService->getPackagingByBranchAndUser($branchId, $userId);
        
        // Return JSON response with packaging data
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $packagingData
        ]);
        exit;
    } catch (\Exception $e) {
        error_log("Error refreshing packaging data: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error refreshing packaging data: ' . $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Refresh distribution data
 */
function refreshDistribution() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $branchId = $_SESSION['branch_id'];
    
    try {
        // Get services from container
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        
        // Get distribution data for the branch and user
        $distributionData = $carbonFootprintService->getDistributionByBranchAndUser($branchId, $userId);
        
        // Return JSON response with distribution data
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $distributionData
        ]);
        exit;
    } catch (\Exception $e) {
        error_log("Error refreshing distribution data: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error refreshing distribution data: ' . $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Refresh all data
 */
function refreshAll() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    $branchId = $_SESSION['branch_id'];
    
    try {
        // Get services from container
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        
        // Get all data for the branch and user
        $productionData = $carbonFootprintService->getProductionByBranchAndUser($branchId, $userId);
        $packagingData = $carbonFootprintService->getPackagingByBranchAndUser($branchId, $userId);
        $distributionData = $carbonFootprintService->getDistributionByBranchAndUser($branchId, $userId);
        $metrics = $carbonFootprintService->getCarbonFootprintMetrics($branchId);
        
        // Return JSON response with all data
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'production' => $productionData,
            'packaging' => $packagingData,
            'distribution' => $distributionData,
            'metrics' => $metrics
        ]);
        exit;
    } catch (\Exception $e) {
        error_log("Error refreshing all data: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error refreshing all data: ' . $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Get production details
 */
function getProductionDetails() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    // Get production ID from query parameters
    $productionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$productionId) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Production ID is required'
        ]);
        exit;
    }
    
    try {
        // Get services from container
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        
        // Get production details
        $production = $carbonFootprintService->getProductionById($productionId);
        
        // Return JSON response with production details
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $production
        ]);
        exit;
    } catch (\Exception $e) {
        error_log("Error getting production details: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error getting production details: ' . $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Get packaging details
 */
function getPackagingDetails() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    // Get packaging ID from query parameters
    $packagingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$packagingId) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Packaging ID is required'
        ]);
        exit;
    }
    
    try {
        // Get services from container
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        
        // Get packaging details
        $packaging = $carbonFootprintService->getPackagingById($packagingId);
        
        // Return JSON response with packaging details
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $packaging
        ]);
        exit;
    } catch (\Exception $e) {
        error_log("Error getting packaging details: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error getting packaging details: ' . $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Get distribution details
 */
function getDistributionDetails() {
    global $container;
    
    // Check if user is logged in and has the correct role
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'BranchUser') {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    // Get distribution ID from query parameters
    $distributionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$distributionId) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Distribution ID is required'
        ]);
        exit;
    }
    
    try {
        // Get services from container
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        
        // Get distribution details
        $distribution = $carbonFootprintService->getDistributionById($distributionId);
        
        // Return JSON response with distribution details
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $distribution
        ]);
        exit;
    } catch (\Exception $e) {
        error_log("Error getting distribution details: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error getting distribution details: ' . $e->getMessage()
        ]);
        exit;
    }
}
?>