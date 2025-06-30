<?php
namespace Services;

use Dao\Interfaces\BranchDAO;
use Models\Branch;
use Models\BranchMetrics;
use Exceptions\DataAccessException;

class BranchService {
    private $branchDao;

    public function __construct(BranchDAO $branchDao) {
        $this->branchDao = $branchDao;
    }

    /**
     * @throws DataAccessException
     */
    public function getBranchById($id) {
        return $this->branchDao->getById($id);
    }

    /**
     * @throws DataAccessException
     */
    public function getAllBranches() {
        return $this->branchDao->getAll();
    }

    /**
     * Get all cities from the database
     * 
     * @return array List of cities
     * @throws DataAccessException
     */
    public function getAllCities() {
        // This is a stub method - cities should be retrieved from a CityDAO
        // Used for backward compatibility
        return [];
    }

    /**
     * Save a branch to the database
     * 
     * @param Branch $branch The branch to save
     * @return mixed Returns branch ID on successful insert, or boolean on update
     * @throws DataAccessException
     */
    public function saveBranch(Branch $branch) {
        $result = $this->branchDao->save($branch);
        
        // For a new branch, return the branch ID
        if ($result === true && $branch->getBranchId() > 0) {
            return $branch->getBranchId();
        }
        
        return $result;
    }

    /**
     * @throws DataAccessException
     */
    public function deleteBranch(Branch $branch) {
        return $this->branchDao->delete($branch);
    }

    /**
     * @throws DataAccessException
     */
    public function getBranchesByCity($cityId) {
        return $this->branchDao->getBranchesByCityId($cityId);
    }

    /**
     * @throws DataAccessException
     */
    public function getBranchMetrics($branchId) {
        return $this->branchDao->getBranchMetrics($branchId);
    }

    /**
     * @throws DataAccessException
     */
    public function getTopBranchesByEmployees($limit) {
        return $this->branchDao->getBranchesWithMostEmployees($limit);
    }
}