<?php
// ReductionStrategy.php
namespace Models;

class ReductionStrategy {
    private $reductionId;
    private $branchId;
    private $userId;
    private $strategy;
    private $statusId;
    private $implementationCosts;
    private $projectedAnnualProfits;
    private $activityDate;

    /**
     * Constructor
     * 
     * @param int $reductionId The reduction ID
     * @param int $branchId The branch ID
     * @param int $userId The user ID
     * @param string $strategy The reduction strategy text
     * @param int $statusId The status ID
     * @param float $implementationCosts The implementation costs
     * @param float $projectedAnnualProfits The projected annual profits
     * @param \DateTime $activityDate The activity date
     */
    public function __construct(
        int $reductionId = 0,
        int $branchId = 0,
        int $userId = 0,
        string $strategy = '',
        int $statusId = 0,
        float $implementationCosts = 0.0,
        float $projectedAnnualProfits = 0.0,
        \DateTime $activityDate = null
    ) {
        $this->reductionId = $reductionId;
        $this->branchId = $branchId;
        $this->userId = $userId;
        $this->strategy = $strategy;
        $this->statusId = $statusId;
        $this->implementationCosts = $implementationCosts;
        $this->projectedAnnualProfits = $projectedAnnualProfits;
        $this->activityDate = $activityDate ?: new \DateTime();
    }

    /**
     * Get the reduction ID
     * 
     * @return int
     */
    public function getReductionId(): int {
        return $this->reductionId;
    }

    /**
     * Set the reduction ID
     * 
     * @param int $reductionId The reduction ID
     * @return self
     */
    public function setReductionId(int $reductionId): self {
        $this->reductionId = $reductionId;
        return $this;
    }

    /**
     * Get the branch ID
     * 
     * @return int
     */
    public function getBranchId(): int {
        return $this->branchId;
    }

    /**
     * Set the branch ID
     * 
     * @param int $branchId The branch ID
     * @return self
     */
    public function setBranchId(int $branchId): self {
        $this->branchId = $branchId;
        return $this;
    }

    /**
     * Get the user ID
     * 
     * @return int
     */
    public function getUserId(): int {
        return $this->userId;
    }

    /**
     * Set the user ID
     * 
     * @param int $userId The user ID
     * @return self
     */
    public function setUserId(int $userId): self {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get the strategy text
     * 
     * @return string
     */
    public function getStrategy(): string {
        return $this->strategy;
    }

    /**
     * Set the strategy text
     * 
     * @param string $strategy The strategy text
     * @return self
     */
    public function setStrategy(string $strategy): self {
        $this->strategy = $strategy;
        return $this;
    }

    /**
     * Get the status ID
     * 
     * @return int
     */
    public function getStatusId(): int {
        return $this->statusId;
    }

    /**
     * Set the status ID
     * 
     * @param int $statusId The status ID
     * @return self
     */
    public function setStatusId(int $statusId): self {
        $this->statusId = $statusId;
        return $this;
    }

    /**
     * Get the implementation costs
     * 
     * @return float
     */
    public function getImplementationCosts(): float {
        return $this->implementationCosts;
    }

    /**
     * Set the implementation costs
     * 
     * @param float $implementationCosts The implementation costs
     * @return self
     */
    public function setImplementationCosts(float $implementationCosts): self {
        $this->implementationCosts = $implementationCosts;
        return $this;
    }

    /**
     * Get the projected annual profits
     * 
     * @return float
     */
    public function getProjectedAnnualProfits(): float {
        return $this->projectedAnnualProfits;
    }

    /**
     * Set the projected annual profits
     * 
     * @param float $projectedAnnualProfits The projected annual profits
     * @return self
     */
    public function setProjectedAnnualProfits(float $projectedAnnualProfits): self {
        $this->projectedAnnualProfits = $projectedAnnualProfits;
        return $this;
    }

    /**
     * Get the activity date
     * 
     * @return \DateTime
     */
    public function getActivityDate(): \DateTime {
        return $this->activityDate;
    }

    /**
     * Set the activity date
     * 
     * @param \DateTime $activityDate The activity date
     * @return self
     */
    public function setActivityDate(\DateTime $activityDate): self {
        $this->activityDate = $activityDate;
        return $this;
    }
}
?>
