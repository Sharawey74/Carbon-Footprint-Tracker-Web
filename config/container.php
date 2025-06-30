<?php
/**
 * Service container configuration
 */

use Utils\DependencyContainer;
use Dao\Impl\UserDAOImpl;
use Dao\Impl\BranchDAOImpl;
use Dao\Impl\AuditLoggingDAOImpl;
use Dao\Impl\CoffeeDistributionDAOImpl;
use Dao\Impl\CoffeePackagingDAOImpl;
use Dao\Impl\CoffeeProductionDAOImpl;
use Dao\Impl\CityDAOImpl;
use Services\UserService;
use Services\BranchService;
use Services\AuditLoggingService;
use Services\CarbonFootprintService;
use Services\ReportGenerationService;
use Services\EmissionService;

// Create container instance
$container = new DependencyContainer();

// Register DAOs
$container->register(UserDAOImpl::class, function($c) {
    global $db;
    return new UserDAOImpl($db);
});

$container->register(BranchDAOImpl::class, function($c) {
    global $db;
    return new BranchDAOImpl($db);
});

$container->register(AuditLoggingDAOImpl::class, function($c) {
    global $db;
    return new AuditLoggingDAOImpl($db);
});

$container->register(CoffeeDistributionDAOImpl::class, function($c) {
    global $db;
    return new CoffeeDistributionDAOImpl($db);
});

$container->register(CoffeePackagingDAOImpl::class, function($c) {
    global $db;
    return new CoffeePackagingDAOImpl($db);
});

$container->register(CoffeeProductionDAOImpl::class, function($c) {
    global $db;
    return new CoffeeProductionDAOImpl($db);
});

$container->register(CityDAOImpl::class, function($c) {
    global $db;
    return new CityDAOImpl($db);
});

$container->register(\Dao\Impl\NotificationDAOImpl::class, function($c) {
    global $db;
    return new \Dao\Impl\NotificationDAOImpl($db);
});

$container->register(\Dao\Impl\ReductionStrategyDAOImpl::class, function($c) {
    global $db;
    return new \Dao\Impl\ReductionStrategyDAOImpl($db);
});

// Register Services
$container->register(UserService::class, function($c) {
    return new UserService($c->get(UserDAOImpl::class));
});

$container->register(BranchService::class, function($c) {
    return new BranchService($c->get(BranchDAOImpl::class));
});

$container->register(AuditLoggingService::class, function($c) {
    return new AuditLoggingService($c->get(AuditLoggingDAOImpl::class));
});

$container->register(CarbonFootprintService::class, function($c) {
    return new CarbonFootprintService(
        $c->get(CoffeeDistributionDAOImpl::class),
        $c->get(CoffeePackagingDAOImpl::class),
        $c->get(CoffeeProductionDAOImpl::class),
        $c->get(BranchDAOImpl::class),
        $c->get(CityDAOImpl::class)
    );
});

$container->register('LanguageService', function($c) {
    return new Services\LanguageService();
});

$container->register(ReportGenerationService::class, function($c) {
    return new ReportGenerationService($c->get('LanguageService'));
});

// Register EmissionService
$container->register(EmissionService::class, function($c) {
    return new EmissionService();
});

// Register NotificationService
$container->register(\Services\NotificationService::class, function($c) {
    return new \Services\NotificationService(
        $c->get(\Dao\Impl\NotificationDAOImpl::class),
        $c->get('LanguageService')
    );
});

// Register ReductionStrategyService
$container->register(\Services\ReductionStrategyService::class, function($c) {
    return new \Services\ReductionStrategyService(
        $c->get(\Dao\Impl\ReductionStrategyDAOImpl::class)
    );
});

// Register other services as needed
$container->register('ProductionService', function($c) {
    return $c->get(CarbonFootprintService::class);
});

$container->register('PackagingService', function($c) {
    return $c->get(CarbonFootprintService::class);
});

$container->register('DistributionService', function($c) {
    return $c->get(CarbonFootprintService::class);
}); 