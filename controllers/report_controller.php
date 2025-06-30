<?php
/**
 * Report Controller for handling report generation and downloads
 */

// Check if direct access
if (!defined('ROOT_PATH')) {
    die('Direct access not permitted');
}

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include required files
use Services\UserService;
use Services\BranchService;
use Services\CarbonFootprintService;
use Services\ReportGenerationService;
use Models\User;
use Models\Branch;

/**
 * Download a carbon footprint report
 */
function download() {
    global $container;
    
    // Debug session information
    error_log("SESSION DEBUG: " . json_encode($_SESSION));
    
    // Check if user is logged in - using a more comprehensive check
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        $_SESSION['error'] = 'You must be logged in to download reports';
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    // Get branch ID from request
    $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0;
    
    if (empty($branchId)) {
        $_SESSION['error'] = 'Branch ID is required';
        header('Location: ' . APP_URL . '/?controller=op_manager&action=dashboard');
        exit;
    }
    
    try {
        // Get required services
        $branchService = $container->get(BranchService::class);
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        
        // Get branch
        $branch = $branchService->getBranchById($branchId);
        if (!$branch) {
            $_SESSION['error'] = 'Branch not found';
            header('Location: ' . APP_URL . '/?controller=op_manager&action=dashboard');
            exit;
        }
        
        // Get carbon footprint metrics for the branch
        $metrics = $carbonFootprintService->getCarbonFootprintMetrics($branchId);
        
        // Include TCPDF library
        require_once(ROOT_PATH . '/vendor/tecnickcom/tcpdf/tcpdf.php');
        
        // Set up PDF constants if they don't exist
        if (!defined('PDF_PAGE_ORIENTATION')) {
            define('PDF_PAGE_ORIENTATION', 'P');
            define('PDF_UNIT', 'mm');
            define('PDF_PAGE_FORMAT', 'A4');
            define('PDF_CREATOR', 'Carbon Tracker');
        }
        
        // Create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Carbon Tracker');
        $pdf->SetTitle('Branch Report');
        $pdf->SetSubject('Carbon Footprint Report');
        $pdf->SetKeywords('Carbon, Footprint, Report');
        
        // Add a page
        $pdf->AddPage();
        
        // Build PDF content
        $html = '<h1>Carbon Footprint Report</h1>';
        $html .= '<h2>Branch: ' . htmlspecialchars($branch->getLocation()) . '</h2>';
        $html .= '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '<table border="1" cellpadding="5" style="width: 100%;">';
        $html .= '<tr><th width="50%">Branch ID</th><td>' . $metrics->getBranchId() . '</td></tr>';
        $html .= '<tr><th>City</th><td>' . htmlspecialchars($metrics->getCityName()) . '</td></tr>';
        $html .= '<tr><th>Production Emissions</th><td>' . number_format($metrics->getProductionEmissions(), 2) . ' kg CO2</td></tr>';
        $html .= '<tr><th>Packaging Emissions</th><td>' . number_format($metrics->getPackagingEmissions(), 2) . ' kg CO2</td></tr>';
        $html .= '<tr><th>Distribution Emissions</th><td>' . number_format($metrics->getDistributionEmissions(), 2) . ' kg CO2</td></tr>';
        $html .= '<tr style="font-weight: bold;"><th>Total Emissions</th><td>' . number_format($metrics->getTotalEmissions(), 2) . ' kg CO2</td></tr>';
        $html .= '</table>';
        
        // Add a pie chart or other visualizations if desired
        
        // Output the PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Send the PDF to the browser
        $pdf->Output('branch_' . $branchId . '_report.pdf', 'D'); // D = force download
        exit;
        
    } catch (Exception $e) {
        error_log("Report download error: " . $e->getMessage());
        $_SESSION['error'] = 'Error generating report: ' . $e->getMessage();
        header('Location: ' . APP_URL . '/?controller=op_manager&action=dashboard');
        exit;
    }
}

/**
 * View a carbon footprint report in the browser
 */
function view() {
    // Similar to download but use 'I' instead of 'D' for the Output parameter
    // 'I' means send to browser inline
    global $container;
    
    // Debug session information
    error_log("SESSION DEBUG VIEW: " . json_encode($_SESSION));
    
    // Check if user is logged in - using a more comprehensive check
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        $_SESSION['error'] = 'You must be logged in to view reports';
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    // Get branch ID from request
    $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0;
    
    if (empty($branchId)) {
        $_SESSION['error'] = 'Branch ID is required';
        header('Location: ' . APP_URL . '/?controller=op_manager&action=dashboard');
        exit;
    }
    
    try {
        // Get required services
        $branchService = $container->get(BranchService::class);
        $carbonFootprintService = $container->get(CarbonFootprintService::class);
        
        // Get branch
        $branch = $branchService->getBranchById($branchId);
        if (!$branch) {
            $_SESSION['error'] = 'Branch not found';
            header('Location: ' . APP_URL . '/?controller=op_manager&action=dashboard');
            exit;
        }
        
        // Get carbon footprint metrics for the branch
        $metrics = $carbonFootprintService->getCarbonFootprintMetrics($branchId);
        
        // Include TCPDF library
        require_once(ROOT_PATH . '/vendor/tecnickcom/tcpdf/tcpdf.php');
        
        // Set up PDF constants if they don't exist
        if (!defined('PDF_PAGE_ORIENTATION')) {
            define('PDF_PAGE_ORIENTATION', 'P');
            define('PDF_UNIT', 'mm');
            define('PDF_PAGE_FORMAT', 'A4');
            define('PDF_CREATOR', 'Carbon Tracker');
        }
        
        // Create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Carbon Tracker');
        $pdf->SetTitle('Branch Report');
        $pdf->SetSubject('Carbon Footprint Report');
        $pdf->SetKeywords('Carbon, Footprint, Report');
        
        // Add a page
        $pdf->AddPage();
        
        // Build PDF content
        $html = '<h1>Carbon Footprint Report</h1>';
        $html .= '<h2>Branch: ' . htmlspecialchars($branch->getLocation()) . '</h2>';
        $html .= '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '<table border="1" cellpadding="5" style="width: 100%;">';
        $html .= '<tr><th width="50%">Branch ID</th><td>' . $metrics->getBranchId() . '</td></tr>';
        $html .= '<tr><th>City</th><td>' . htmlspecialchars($metrics->getCityName()) . '</td></tr>';
        $html .= '<tr><th>Production Emissions</th><td>' . number_format($metrics->getProductionEmissions(), 2) . ' kg CO2</td></tr>';
        $html .= '<tr><th>Packaging Emissions</th><td>' . number_format($metrics->getPackagingEmissions(), 2) . ' kg CO2</td></tr>';
        $html .= '<tr><th>Distribution Emissions</th><td>' . number_format($metrics->getDistributionEmissions(), 2) . ' kg CO2</td></tr>';
        $html .= '<tr style="font-weight: bold;"><th>Total Emissions</th><td>' . number_format($metrics->getTotalEmissions(), 2) . ' kg CO2</td></tr>';
        $html .= '</table>';
        
        // Output the PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Send the PDF to the browser inline (view in browser)
        $pdf->Output('branch_' . $branchId . '_report.pdf', 'I'); // I = inline display
        exit;
        
    } catch (Exception $e) {
        error_log("Report view error: " . $e->getMessage());
        $_SESSION['error'] = 'Error viewing report: ' . $e->getMessage();
        header('Location: ' . APP_URL . '/?controller=op_manager&action=dashboard');
        exit;
    }
} 