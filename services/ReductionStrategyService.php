<?php
namespace Services;

use Dao\Interfaces\ReductionStrategyDAO;
use Models\ReductionStrategy;
use Exceptions\DataAccessException;

class ReductionStrategyService {
    private $reductionStrategyDao;

    public function __construct(ReductionStrategyDAO $reductionStrategyDao) {
        $this->reductionStrategyDao = $reductionStrategyDao;
    }

    /**
     * Get all reduction strategies
     * 
     * @return array Array of ReductionStrategy objects
     * @throws DataAccessException
     */
    public function getAllStrategies(): array {
        return $this->reductionStrategyDao->getAll();
    }

    /**
     * Get a strategy by ID
     * 
     * @param int $strategyId Strategy ID
     * @return ReductionStrategy|null The strategy or null if not found
     * @throws DataAccessException
     */
    public function getStrategyById(int $strategyId): ?ReductionStrategy {
        return $this->reductionStrategyDao->getById($strategyId);
    }

    /**
     * Get strategies for a specific branch
     * 
     * @param int $branchId Branch ID
     * @return array Array of ReductionStrategy objects
     * @throws DataAccessException
     */
    public function getStrategiesByBranchId(int $branchId): array {
        return $this->reductionStrategyDao->getByBranchId($branchId);
    }

    /**
     * Update a reduction plan status
     * 
     * @param int $planId Plan ID
     * @param int $statusId New status ID
     * @return bool Success status
     * @throws DataAccessException
     */
    public function updatePlanStatus(int $planId, int $statusId): bool {
        $strategy = $this->reductionStrategyDao->getById($planId);
        if (!$strategy) {
            return false;
        }
        
        $strategy->setStatusId($statusId);
        return $this->reductionStrategyDao->update($strategy);
    }

    /**
     * Save a new reduction strategy
     * 
     * @param ReductionStrategy $strategy The strategy to save
     * @return bool Success status
     * @throws DataAccessException
     */
    public function saveStrategy(ReductionStrategy $strategy): bool {
        return $this->reductionStrategyDao->save($strategy);
    }

    /**
     * Delete a reduction strategy
     * 
     * @param int $strategyId Strategy ID
     * @return bool Success status
     * @throws DataAccessException
     */
    public function deleteStrategy(int $strategyId): bool {
        $strategy = $this->reductionStrategyDao->getById($strategyId);
        if (!$strategy) {
            return false;
        }
        
        return $this->reductionStrategyDao->delete($strategy);
    }
}