<?php
// branch_entry_view.php
$currentTab = $_GET['tab'] ?? 'production';
$coffeeTypes = ['Arabica Beans', 'Robusta Beans', 'Organic Beans'];
$productTypes = ['Ground', 'Whole Bean', 'Instant'];
$vehicleTypes = ['Minivan', 'Pickup Truck'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Branch Data Entry</title>
    <style>
        .tab-content { display: none; }
        .active { display: block; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Branch <?= $_SESSION['branch_id'] ?> - Data Entry</h1>
    
    <!-- Tabs -->
    <div class="tabs">
        <a href="?tab=production">Production</a>
        <a href="?tab=packaging">Packaging</a>
        <a href="?tab=distribution">Distribution</a>
    </div>

    <!-- Production Tab -->
    <div class="tab-content <?= $currentTab === 'production' ? 'active' : '' ?>">
        <h2>Production Data</h2>
        <?php include 'production_view.php'; ?>
    </div>

    <!-- Packaging Tab -->
    <div class="tab-content <?= $currentTab === 'packaging' ? 'active' : '' ?>">
        <h2>Packaging Data</h2>
        <?php include 'packaging_view.php'; ?>
    </div>

    <!-- Distribution Tab -->
    <div class="tab-content <?= $currentTab === 'distribution' ? 'active' : '' ?>">
        <h2>Distribution Data</h2>
        <?php include 'distribution_view.php'; ?>
    </div>
</body>
</html>