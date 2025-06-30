<?php
namespace Services;

use Dao\Interfaces\CoffeeProductionDAO;
use Dao\Interfaces\CoffeePackagingDAO;
use Dao\Interfaces\CoffeeDistributionDAO;
use Models\Notification;
use Exceptions\DataAccessException;
use DateTime;
use Exception;

class ThresholdAlertService {
    private $productionDao;
    private $packagingDao;
    private $distributionDao;
    private $notificationService;
    private $thresholds;

    public function __construct(
        CoffeeProductionDAO $productionDao,
        CoffeePackagingDAO $packagingDao,
        CoffeeDistributionDAO $distributionDao,
        NotificationService $notificationService
    ) {
        $this->productionDao = $productionDao;
        $this->packagingDao = $packagingDao;
        $this->distributionDao = $distributionDao;
        $this->notificationService = $notificationService;
        $this->thresholds = $this->loadDefaultThresholds();
    }

    /**
     * Check if any emission thresholds have been exceeded for a branch
     * 
     * @param int $branchId Branch ID to check
     * @param int $userId User ID to notify
     * @throws DataAccessException if data access fails
     */
    public function checkBranchThresholds($branchId, $userId) {
        try {
            $productionEmissions = $this->productionDao->getTotalCarbonEmissionsByBranchId($branchId);
            $packagingEmissions = $this->packagingDao->getTotalCarbonEmissionsByBranchId($branchId);
            $distributionEmissions = $this->distributionDao->getTotalCarbonEmissionsByBranchId($branchId);

            $this->checkAndNotify('production', $productionEmissions, $branchId, $userId);
            $this->checkAndNotify('packaging', $packagingEmissions, $branchId, $userId);
            $this->checkAndNotify('distribution', $distributionEmissions, $branchId, $userId);
        } catch (Exception $e) {
            error_log("Threshold check failed: " . $e->getMessage());
            throw new DataAccessException("Threshold check failed: " . $e->getMessage());
        }
    }

    /**
     * Check if a threshold is exceeded and send notification if it is
     * 
     * @param string $type Emission type
     * @param float $value Current emission value
     * @param int $branchId Branch ID
     * @param int $userId User ID to notify
     * @throws DataAccessException if notification sending fails
     */
    private function checkAndNotify($type, $value, $branchId, $userId) {
        if ($value > $this->thresholds[$type]) {
            $message = sprintf(
                "Threshold exceeded for %s emissions in branch %d: %.2f kg CO2",
                $type, $branchId, $value
            );
            
            $notification = new Notification(
                null, 
                $userId, 
                $message, 
                new DateTime(), 
                false
            );
            
            $this->notificationService->sendNotification($notification);
        }
    }

    /**
     * Load default threshold values
     * 
     * @return array Default threshold values for each emission type
     */
    private function loadDefaultThresholds() {
        return [
            'production' => 1000.0, // kg CO2
            'packaging' => 500.0, 
            'distribution' => 1500.0
        ];
    }

    /**
     * Set a threshold value for an emission type
     * 
     * @param string $type Emission type
     * @param float $value Threshold value in kg CO2
     */
    public function setThreshold($type, $value) {
        $this->thresholds[$type] = $value;
    }
}
?>