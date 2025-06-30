<?php
namespace Dao\Interfaces;

use Models\AuditLogging;
use Exceptions\DataAccessException;
use DateTime;

interface AuditLoggingDAO extends DAO {
    public function getLogsByUserId(int $userId): array;
    public function getLogsByAction(string $action): array;
    public function getLogsByDateRange(DateTime $startDate, DateTime $endDate): array;
    public function deleteAllLogs(): bool;
    public function deleteLogsOlderThan(DateTime $timestamp): bool;
}
?>