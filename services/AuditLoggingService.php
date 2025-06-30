<?php
// src/Service/AuditLoggingService.php
namespace Services;

use Dao\Interfaces\AuditLoggingDAO;
use Models\AuditLogging;
use Exceptions\DataAccessException;
use DateTime;

class AuditLoggingService {
    private $auditLoggingDao;

    public function __construct(AuditLoggingDAO $auditLoggingDao) {
        $this->auditLoggingDao = $auditLoggingDao;
    }

    /**
     * @throws DataAccessException
     */
    public function logAction(int $userId, string $action, string $tableName, ?int $recordId = null): void {
        $log = new AuditLogging(
            0,
            $userId,
            $action,
            $tableName,
            $recordId,
            new DateTime()
        );
        $this->auditLoggingDao->insert($log);
    }

    /**
     * @throws DataAccessException
     */
    public function getLogsByUser(int $userId): array {
        return $this->auditLoggingDao->getLogsByUserId($userId);
    }

    /**
     * @throws DataAccessException
     */
    public function getRecentLogs(): array {
        return $this->auditLoggingDao->getAll();
    }
}