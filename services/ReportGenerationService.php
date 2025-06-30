<?php
namespace Services;

use Models\CarbonFootprintMetrics;
use Exceptions\ReportGenerationException;
use TCPDF;
use Models\ReductionStrategy;

class ReportGenerationService {
    /**
     * @throws ReportGenerationException
     */
    public function generateCarbonReport(CarbonFootprintMetrics $metrics) {
        try {
            // Include TCPDF library
            require_once(ROOT_PATH . '/vendor/tecnickcom/tcpdf/tcpdf.php');
            
            // Define constants if not defined
            if (!defined('PDF_PAGE_ORIENTATION')) {
                define('PDF_PAGE_ORIENTATION', 'P');
                define('PDF_UNIT', 'mm');
                define('PDF_PAGE_FORMAT', 'A4');
                define('PDF_CREATOR', 'Carbon Tracker');
            }
            
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle('Carbon Footprint Report');
            $pdf->AddPage();

            // Build PDF content
            $html = '<h1>Carbon Footprint Report</h1>';
            $html .= '<table>';
            $html .= '<tr><th>Branch ID</th><td>'.$metrics->getBranchId().'</td></tr>';
            $html .= '<tr><th>City</th><td>'.$metrics->getCityName().'</td></tr>';
            $html .= '<tr><th>Production Emissions</th><td>'.$metrics->getProductionEmissions().' kg CO2</td></tr>';
            $html .= '<tr><th>Packaging Emissions</th><td>'.$metrics->getPackagingEmissions().' kg CO2</td></tr>';
            $html .= '<tr><th>Distribution Emissions</th><td>'.$metrics->getDistributionEmissions().' kg CO2</td></tr>';
            $html .= '<tr><th>Total Emissions</th><td>'.$metrics->getTotalEmissions().' kg CO2</td></tr>';
            $html .= '</table>';

            $pdf->writeHTML($html, true, false, true, false, '');
            
            // Try an alternative approach - output directly to browser
            // This will work immediately for testing
            $filename = 'branch_' . $metrics->getBranchId() . '_report.pdf';
            
            // For direct output to browser
            $pdf->Output($filename, 'I');
            
            return true;
        } catch (\Exception $e) {
            error_log("Report generation error: " . $e->getMessage());
            throw new ReportGenerationException("Failed to generate carbon report: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate a city-wide emissions report
     * 
     * @param string $cityName The name of the city
     * @param array $cityData Array of data for branches in the city
     * @throws ReportGenerationException
     */
    public function generateCityReport(string $cityName, array $cityData) {
        try {
            // Include TCPDF library
            require_once(ROOT_PATH . '/vendor/tecnickcom/tcpdf/tcpdf.php');
            
            // Define constants if not defined
            if (!defined('PDF_PAGE_ORIENTATION')) {
                define('PDF_PAGE_ORIENTATION', 'P');
                define('PDF_UNIT', 'mm');
                define('PDF_PAGE_FORMAT', 'A4');
                define('PDF_CREATOR', 'Carbon Tracker');
            }
            
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle($cityName . ' Carbon Emissions Report');
            $pdf->AddPage();

            // Calculate totals
            $totalProduction = 0;
            $totalPackaging = 0;
            $totalDistribution = 0;
            $totalEmissions = 0;
            
            foreach ($cityData as $branch) {
                $totalProduction += $branch['production'];
                $totalPackaging += $branch['packaging'];
                $totalDistribution += $branch['distribution'];
                $totalEmissions += $branch['total'];
            }

            // Build PDF content
            $html = '<h1>' . $cityName . ' Carbon Emissions Report</h1>';
            $html .= '<h2>Summary</h2>';
            
            // Summary table
            $html .= '<table border="1" cellpadding="5">';
            $html .= '<tr style="background-color: #f2f2f2; font-weight: bold;">';
            $html .= '<th>Emission Source</th><th>Total (kg CO2)</th>';
            $html .= '</tr>';
            $html .= '<tr><td>Production</td><td>' . number_format($totalProduction, 2) . '</td></tr>';
            $html .= '<tr><td>Packaging</td><td>' . number_format($totalPackaging, 2) . '</td></tr>';
            $html .= '<tr><td>Distribution</td><td>' . number_format($totalDistribution, 2) . '</td></tr>';
            $html .= '<tr style="font-weight: bold;"><td>TOTAL</td><td>' . number_format($totalEmissions, 2) . '</td></tr>';
            $html .= '</table>';
            
            // Branch details
            $html .= '<h2>Branch Details</h2>';
            $html .= '<table border="1" cellpadding="5">';
            $html .= '<tr style="background-color: #f2f2f2; font-weight: bold;">';
            $html .= '<th>Branch ID</th><th>Branch Name</th><th>Production (kg)</th>';
            $html .= '<th>Packaging (kg)</th><th>Distribution (kg)</th><th>Total (kg)</th>';
            $html .= '</tr>';
            
            foreach ($cityData as $branch) {
                $html .= '<tr>';
                $html .= '<td>' . $branch['branch_id'] . '</td>';
                $html .= '<td>' . $branch['branch_name'] . '</td>';
                $html .= '<td>' . number_format($branch['production'], 2) . '</td>';
                $html .= '<td>' . number_format($branch['packaging'], 2) . '</td>';
                $html .= '<td>' . number_format($branch['distribution'], 2) . '</td>';
                $html .= '<td><strong>' . number_format($branch['total'], 2) . '</strong></td>';
                $html .= '</tr>';
            }
            
            $html .= '</table>';
            
            // Add date of report generation
            $html .= '<p style="font-size: 10px;">Report generated on: ' . date('Y-m-d H:i:s') . '</p>';

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output($cityName . '_emissions_report.pdf', 'I');
        } catch (\Exception $e) {
            throw new ReportGenerationException("Failed to generate city report: " . $e->getMessage());
        }
    }

    /**
     * @throws ReportGenerationException
     */
    public function generateComparativeReport(array $branches) {
        try {
            // Include TCPDF library
            require_once(ROOT_PATH . '/vendor/tecnickcom/tcpdf/tcpdf.php');
            
            // Define constants if not defined
            if (!defined('PDF_CREATOR')) {
                define('PDF_CREATOR', 'Carbon Tracker');
            }
            
            $pdf = new \TCPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle('Comparative Carbon Footprint Report');
            $pdf->AddPage();
            
            $html = '<h1>Comparative Carbon Footprint Report</h1>';
            $html .= '<table border="1">';
            $html .= '<tr><th>Branch ID</th><th>Location</th><th>Total Emissions</th></tr>';
            
            foreach ($branches as $branch) {
                $html .= '<tr>';
                $html .= '<td>' . $branch->getBranchId() . '</td>';
                $html .= '<td>' . $branch->getLocation() . '</td>';
                $html .= '<td>' . $branch->getCarbonEmissionsKg() . ' kg CO2</td>';
                $html .= '</tr>';
            }
            
            $html .= '</table>';
            
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output('comparative_report.pdf', 'I');
        } catch (\Exception $e) {
            throw new ReportGenerationException("Failed to generate comparative report: " . $e->getMessage());
        }
    }
    
    /**
     * Generate a report for a specific reduction plan
     * 
     * @param ReductionStrategy $plan The reduction plan object
     * @throws ReportGenerationException
     */
    public function generateReductionPlanReport(ReductionStrategy $plan) {
        try {
            // Include TCPDF library
            require_once(ROOT_PATH . '/vendor/tecnickcom/tcpdf/tcpdf.php');
            
            // Define constants if not defined
            if (!defined('PDF_PAGE_ORIENTATION')) {
                define('PDF_PAGE_ORIENTATION', 'P');
                define('PDF_UNIT', 'mm');
                define('PDF_PAGE_FORMAT', 'A4');
                define('PDF_CREATOR', 'Carbon Tracker');
            }
            
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle('Reduction Plan Report');
            $pdf->AddPage();

            // Get status name
            $statusMap = [
                1 => 'Pending',
                2 => 'Approved',
                3 => 'In Progress',
                4 => 'Completed',
                5 => 'Rejected'
            ];
            
            $statusName = $statusMap[$plan->getStatusId()] ?? 'Unknown';
            
            // Calculate ROI
            $costs = $plan->getImplementationCosts();
            $profits = $plan->getProjectedAnnualProfits();
            $roi = ($costs > 0) ? (($profits - $costs) / $costs) * 100 : 0;

            // Build PDF content
            $html = '<h1>Reduction Plan Report</h1>';
            $html .= '<table border="1" cellpadding="5">';
            $html .= '<tr><th>Plan ID</th><td>' . $plan->getReductionId() . '</td></tr>';
            $html .= '<tr><th>Branch ID</th><td>' . $plan->getBranchId() . '</td></tr>';
            $html .= '<tr><th>Strategy</th><td>' . $plan->getStrategy() . '</td></tr>';
            $html .= '<tr><th>Implementation Costs</th><td>$' . number_format($costs, 2) . '</td></tr>';
            $html .= '<tr><th>Projected Annual Profits</th><td>$' . number_format($profits, 2) . '</td></tr>';
            $html .= '<tr><th>ROI</th><td>' . number_format($roi, 1) . '%</td></tr>';
            $html .= '<tr><th>Status</th><td>' . $statusName . '</td></tr>';
            $html .= '</table>';
            
            // Add date of report generation
            $html .= '<p style="font-size: 10px;">Report generated on: ' . date('Y-m-d H:i:s') . '</p>';

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output('reduction_plan_report.pdf', 'I');
        } catch (\Exception $e) {
            throw new ReportGenerationException("Failed to generate reduction plan report: " . $e->getMessage());
        }
    }
}