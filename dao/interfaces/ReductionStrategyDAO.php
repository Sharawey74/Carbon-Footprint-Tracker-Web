<?php
namespace Dao\Interfaces;

use Models\ReductionStrategy;
use Exceptions\DataAccessException;

interface ReductionStrategyDAO extends DAO {
    /**
     * Get strategies by branch ID
     * 
     * @param int $branchId Branch ID
     * @return array Array of ReductionStrategy objects
     * @throws DataAccessException
     */
    public function getByBranchId(int $branchId): array;
    
    /**
     * Get total implementation costs by branch ID
     * 
     * @param int $branchId Branch ID
     * @return float Total implementation costs
     * @throws DataAccessException
     */
    public function getTotalImplementationCostsByBranchId(int $branchId): float;
    
    /**
     * Get strategies by status ID
     * 
     * @param int $statusId Status ID
     * @return array Array of ReductionStrategy objects
     * @throws DataAccessException
     */
    public function getByStatusId(int $statusId): array;
}
?>