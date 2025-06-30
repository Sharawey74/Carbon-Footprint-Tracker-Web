<div class="container mt-4">
    <div class="dashboard-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="dashboard-title"><i class="fas fa-chart-line me-2"></i>Branch #<?= htmlspecialchars($_SESSION['branch_id']) ?> Dashboard</h2>
            <div class="dashboard-actions">
                <button class="btn btn-primary btn-rounded me-2" onclick="refreshAll()">
                    <i class="fas fa-sync-alt me-1"></i> Refresh Data
                </button>
                <div class="dropdown d-inline-block">
                    <button class="btn btn-light btn-rounded dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="fas fa-print me-2"></i>Print Dashboard</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportData()"><i class="fas fa-file-export me-2"></i>Export Data</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#helpModal"><i class="fas fa-question-circle me-2"></i>Help</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <!-- Summary Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card elevation-2">
                <div class="stats-icon bg-gradient-primary">
                    <i class="fas fa-industry"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number"><?= $productionData ? count($productionData) : 0 ?></div>
                    <div class="stats-label">Production Records</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card elevation-2">
                <div class="stats-icon bg-gradient-success">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number"><?= $packagingData ? count($packagingData) : 0 ?></div>
                    <div class="stats-label">Packaging Records</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card elevation-2">
                <div class="stats-icon bg-gradient-info">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number"><?= $distributionData ? count($distributionData) : 0 ?></div>
                    <div class="stats-label">Distribution Records</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card elevation-2">
                <div class="stats-icon bg-gradient-warning">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number">
                        <?php
                        $totalEmissions = 0;
                        if ($productionData) {
                            foreach ($productionData as $record) {
                                $totalEmissions += $record->getPrCarbonEmissionsKG() ?? 0;
                            }
                        }
                        if ($packagingData) {
                            foreach ($packagingData as $record) {
                                $totalEmissions += $record->getPaCarbonEmissionsKG() ?? 0;
                            }
                        }
                        if ($distributionData) {
                            foreach ($distributionData as $record) {
                                $totalEmissions += $record->getVCarbonEmissionsKg() ?? 0;
                            }
                        }
                        echo number_format($totalEmissions, 1);
                        ?>
                    </div>
                    <div class="stats-label">Total Emissions (KG)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Tabs -->
    <ul class="nav nav-tabs custom-tabs mb-0" id="branchTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="production-tab" data-bs-toggle="tab" data-bs-target="#production" type="button" role="tab" aria-controls="production" aria-selected="true">
                <i class="fas fa-industry me-2"></i>Production
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="packaging-tab" data-bs-toggle="tab" data-bs-target="#packaging" type="button" role="tab" aria-controls="packaging" aria-selected="false">
                <i class="fas fa-box me-2"></i>Packaging
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="distribution-tab" data-bs-toggle="tab" data-bs-target="#distribution" type="button" role="tab" aria-controls="distribution" aria-selected="false">
                <i class="fas fa-truck me-2"></i>Distribution
            </button>
        </li>
    </ul>

    <div class="tab-content p-0" id="branchTabContent">
        <div class="tab-container rounded-bottom shadow-sm border border-top-0 p-4">
        <!-- Production Tab -->
        <div class="tab-pane fade show active" id="production" role="tabpanel" aria-labelledby="production-tab">
            <div class="row">
                <div class="col-md-4">
                        <div class="card shadow-hover">
                        <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Update Production Data</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            // Get the latest production record
                            $latestProduction = !empty($productionData) ? reset($productionData) : null;
                            ?>
                                <form method="POST" action="<?= APP_URL ?>/?controller=branch&action=updateProduction" class="needs-validation">
                                <?php if ($latestProduction): ?>
                                <input type="hidden" name="production_id" value="<?= htmlspecialchars($latestProduction->getProductionId() ?? '') ?>">
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label for="supplier" class="form-label">Supplier</label>
                                    <input type="text" class="form-control" id="supplier" name="supplier" value="<?= htmlspecialchars($latestProduction ? ($latestProduction->getSupplier() ?? '') : '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="coffeeType" class="form-label">Coffee Type</label>
                                    <select class="form-select" id="coffeeType" name="coffeeType" required>
                                        <option value="">Select Coffee Type</option>
                                        <option value="Arabica Beans" <?= $latestProduction && $latestProduction->getCoffeeType() == 'Arabica Beans' ? 'selected' : '' ?>>Arabica Beans</option>
                                        <option value="Robusta Beans" <?= $latestProduction && $latestProduction->getCoffeeType() == 'Robusta Beans' ? 'selected' : '' ?>>Robusta Beans</option>
                                        <option value="Organic Beans" <?= $latestProduction && $latestProduction->getCoffeeType() == 'Organic Beans' ? 'selected' : '' ?>>Organic Beans</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="productType" class="form-label">Product Type</label>
                                    <select class="form-select" id="productType" name="productType" required>
                                        <option value="">Select Product Type</option>
                                        <option value="Ground" <?= $latestProduction && $latestProduction->getProductType() == 'Ground' ? 'selected' : '' ?>>Ground</option>
                                        <option value="Whole Bean" <?= $latestProduction && $latestProduction->getProductType() == 'Whole Bean' ? 'selected' : '' ?>>Whole Bean</option>
                                        <option value="Instant" <?= $latestProduction && $latestProduction->getProductType() == 'Instant' ? 'selected' : '' ?>>Instant</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity (KG)</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($latestProduction ? ($latestProduction->getProductionQuantitiesOfCoffeeKG() ?? '0.01') : '0.01') ?>" required>
                                </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i>Update Production Data
                                    </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                        <div class="card shadow-hover">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Production Records</h5>
                                <button class="btn btn-light btn-sm" onclick="refreshProduction()">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Supplier</th>
                                            <th>Coffee Type</th>
                                            <th>Product Type</th>
                                            <th>Quantity (KG)</th>
                                            <th>Emissions (KG)</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($productionData)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No production records found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($productionData as $record): ?>
                                                    <tr class="<?= $record === reset($productionData) ? 'new-data' : '' ?>">
                                                    <td><?= htmlspecialchars($record->getProductionId() ?? '') ?></td>
                                                    <td><?= htmlspecialchars($record->getSupplier() ?? '') ?></td>
                                                    <td><?= htmlspecialchars($record->getCoffeeType() ?? '') ?></td>
                                                    <td><?= htmlspecialchars($record->getProductType() ?? '') ?></td>
                                                    <td><?= htmlspecialchars($record->getProductionQuantitiesOfCoffeeKG() ?? '') ?></td>
                                                        <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($record->getPrCarbonEmissionsKG() ?? '') ?></span></td>
                                                    <td><?= $record->getActivityDate() ? htmlspecialchars($record->getActivityDate()->format('Y-m-d')) : '' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Packaging Tab -->
        <div class="tab-pane fade" id="packaging" role="tabpanel" aria-labelledby="packaging-tab">
            <div class="row">
                <div class="col-md-4">
                        <div class="card shadow-hover">
                        <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-box me-2"></i>Update Packaging Data</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            // Get the latest packaging record
                            $latestPackaging = !empty($packagingData) ? reset($packagingData) : null;
                            ?>
                                <form method="POST" action="<?= APP_URL ?>/?controller=branch&action=updatePackaging" class="needs-validation">
                                <?php if ($latestPackaging): ?>
                                <input type="hidden" name="packaging_id" value="<?= htmlspecialchars($latestPackaging->getPackagingId() ?? '') ?>">
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label for="waste" class="form-label">Packaging Waste (KG)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="waste" name="waste" value="<?= htmlspecialchars($latestPackaging ? ($latestPackaging->getPackagingWasteKG() ?? '0') : '0') ?>" required>
                                        <div class="form-text text-muted">Enter the total packaging waste in kilograms</div>
                                </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-save me-2"></i>Update Packaging Data
                                    </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                        <div class="card shadow-hover">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Packaging Records</h5>
                                <button class="btn btn-light btn-sm" onclick="refreshPackaging()">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Waste (KG)</th>
                                            <th>Emissions (KG)</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($packagingData)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No packaging records found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($packagingData as $record): ?>
                                                    <tr class="<?= $record === reset($packagingData) ? 'new-data' : '' ?>">
                                                    <td><?= htmlspecialchars($record->getPackagingId() ?? '') ?></td>
                                                    <td><?= htmlspecialchars($record->getPackagingWasteKG() ?? '') ?></td>
                                                        <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($record->getPaCarbonEmissionsKG() ?? '') ?></span></td>
                                                    <td><?= $record->getActivityDate() ? htmlspecialchars($record->getActivityDate()->format('Y-m-d')) : '' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution Tab -->
        <div class="tab-pane fade" id="distribution" role="tabpanel" aria-labelledby="distribution-tab">
            <div class="row">
                <div class="col-md-4">
                        <div class="card shadow-hover">
                        <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Update Distribution Data</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            // Get the latest distribution record
                            $latestDistribution = !empty($distributionData) ? reset($distributionData) : null;
                            ?>
                                <form method="POST" action="<?= APP_URL ?>/?controller=branch&action=updateDistribution" class="needs-validation">
                                <?php if ($latestDistribution): ?>
                                <input type="hidden" name="distribution_id" value="<?= htmlspecialchars($latestDistribution->getDistributionId() ?? '') ?>">
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label for="vehicleType" class="form-label">Vehicle Type</label>
                                    <select class="form-select" id="vehicleType" name="vehicleType" required>
                                        <option value="">Select Vehicle Type</option>
                                        <option value="Minivan" <?= $latestDistribution && $latestDistribution->getVehicleType() == 'Minivan' ? 'selected' : '' ?>>Minivan</option>
                                        <option value="Pickup Truck" <?= $latestDistribution && $latestDistribution->getVehicleType() == 'Pickup Truck' ? 'selected' : '' ?>>Pickup Truck</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="numVehicles" class="form-label">Number of Vehicles</label>
                                    <input type="number" min="1" class="form-control" id="numVehicles" name="numVehicles" value="<?= htmlspecialchars($latestDistribution ? ($latestDistribution->getNumberOfVehicles() ?? '1') : '1') ?>" required>
                                </div>
                                <div class="mb-3">
                                        <label for="distance" class="form-label">Distance Per Vehicle (KM)</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control" id="distance" name="distance" value="<?= htmlspecialchars($latestDistribution ? ($latestDistribution->getDistancePerVehicleKM() ?? '0.01') : '0.01') ?>" required>
                                </div>
                                    <button type="submit" class="btn btn-info w-100 text-white">
                                        <i class="fas fa-save me-2"></i>Update Distribution Data
                                    </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                        <div class="card shadow-hover">
                            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Distribution Records</h5>
                                <button class="btn btn-light btn-sm" onclick="refreshDistribution()">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Vehicle Type</th>
                                                <th>Number of Vehicles</th>
                                                <th>Distance (KM)</th>
                                            <th>Total Distance (KM)</th>
                                            <th>Emissions (KG)</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($distributionData)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No distribution records found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($distributionData as $record): ?>
                                                    <tr class="<?= $record === reset($distributionData) ? 'new-data' : '' ?>">
                                                    <td><?= htmlspecialchars($record->getDistributionId() ?? '') ?></td>
                                                    <td><?= htmlspecialchars($record->getVehicleType() ?? '') ?></td>
                                                    <td><?= htmlspecialchars($record->getNumberOfVehicles() ?? '') ?></td>
                                                    <td><?= htmlspecialchars($record->getDistancePerVehicleKM() ?? '') ?></td>
                                                    <td><?= htmlspecialchars($record->getTotalDistanceKM() ?? '') ?></td>
                                                        <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($record->getVCarbonEmissionsKg() ?? '') ?></span></td>
                                                    <td><?= $record->getActivityDate() ? htmlspecialchars($record->getActivityDate()->format('Y-m-d')) : '' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
</div>

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="helpModalLabel">Dashboard Help</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Using the Dashboard</h6>
                <p>This dashboard allows you to track and update carbon footprint data for your branch across three categories:</p>
                <ul>
                    <li><strong>Production</strong> - Coffee production details and emissions</li>
                    <li><strong>Packaging</strong> - Packaging waste and resulting emissions</li>
                    <li><strong>Distribution</strong> - Vehicle usage and transportation emissions</li>
                </ul>
                <h6>Updating Data</h6>
                <p>Use the forms on the left side of each tab to update your latest records. Your changes will be automatically reflected in the tables.</p>
                <h6>Need More Help?</h6>
                <p>Contact the support team at <a href="mailto:support@carbontracker.com">support@carbontracker.com</a></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for refresh functions -->
<script>
function refreshProduction() {
    fetch('<?= APP_URL ?>/?controller=branch&action=refreshProduction')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Production data refreshed successfully', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification('Error refreshing production data', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error refreshing production data', 'danger');
        });
}

function refreshPackaging() {
    fetch('<?= APP_URL ?>/?controller=branch&action=refreshPackaging')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Packaging data refreshed successfully', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification('Error refreshing packaging data', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error refreshing packaging data', 'danger');
        });
}

function refreshDistribution() {
    fetch('<?= APP_URL ?>/?controller=branch&action=refreshDistribution')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Distribution data refreshed successfully', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification('Error refreshing distribution data', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error refreshing distribution data', 'danger');
        });
}

function refreshAll() {
    fetch('<?= APP_URL ?>/?controller=branch&action=refreshAll')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('All data refreshed successfully', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification('Error refreshing data', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error refreshing data', 'danger');
        });
}

function exportData() {
    window.location.href = '<?= APP_URL ?>/?controller=branch&action=exportData';
}
</script>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> 

<!-- Custom JavaScript to fix tab display -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix tab display issues
        const tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
        
        // Function to handle tab display
        function showActiveTab() {
            // Get active tab
            const activeTab = document.querySelector('.tab-pane.active');
            if (activeTab) {
                activeTab.style.display = 'block';
                activeTab.style.opacity = '1';
                activeTab.style.position = 'relative';
            }
            
            // Hide non-active tabs
            const inactiveTabs = document.querySelectorAll('.tab-pane:not(.active)');
            inactiveTabs.forEach(tab => {
                tab.style.display = 'none';
                tab.style.opacity = '0';
                tab.style.position = 'absolute';
            });
        }
        
        // Initial setup
        showActiveTab();
        
        // Add event listeners to tabs
        tabLinks.forEach(tabLink => {
            tabLink.addEventListener('shown.bs.tab', function() {
                showActiveTab();
            });
        });
    });
</script>

<!-- Custom CSS for enhanced dashboard -->
<style>
    /* Dashboard Header Styling */
    .dashboard-header {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 1rem;
    }
    
    .dashboard-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    
    /* Button styling */
    .btn-rounded {
        border-radius: 50px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #3498db, #2980b9);
        border: none;
        box-shadow: 0 4px 6px rgba(52, 152, 219, 0.2);
    }
    
    .btn-primary:hover {
        background: linear-gradient(45deg, #2980b9, #3498db);
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(52, 152, 219, 0.3);
    }
    
    .btn-success {
        background: linear-gradient(45deg, #2ecc71, #27ae60);
        border: none;
        box-shadow: 0 4px 6px rgba(46, 204, 113, 0.2);
    }
    
    .btn-success:hover {
        background: linear-gradient(45deg, #27ae60, #2ecc71);
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(46, 204, 113, 0.3);
    }
    
    .btn-info {
        background: linear-gradient(45deg, #3498db, #00b0ff);
        border: none;
        box-shadow: 0 4px 6px rgba(0, 176, 255, 0.2);
    }
    
    .btn-info:hover {
        background: linear-gradient(45deg, #00b0ff, #3498db);
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0, 176, 255, 0.3);
    }
    
    /* Stats card styling */
    .stats-card {
        display: flex;
        padding: 1.25rem;
        background-color: #fff;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .elevation-2 {
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.08), 0 0 6px rgba(0, 0, 0, 0.05);
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12), 0 4px 8px rgba(0, 0, 0, 0.06);
    }
    
    .stats-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 12px;
        margin-right: 15px;
        font-size: 24px;
        color: white;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(45deg, #3498db, #2980b9);
    }
    
    .bg-gradient-success {
        background: linear-gradient(45deg, #2ecc71, #27ae60);
    }
    
    .bg-gradient-info {
        background: linear-gradient(45deg, #3498db, #00b0ff);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(45deg, #f39c12, #f1c40f);
    }
    
    .stats-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .stats-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
    }
    
    .stats-label {
        font-size: 0.85rem;
        color: #7f8c8d;
        font-weight: 500;
    }
    
    /* Tab styling */
    .custom-tabs .nav-link {
        padding: 12px 24px;
        border: none;
        border-radius: 8px 8px 0 0;
        font-weight: 500;
        color: #7f8c8d;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .custom-tabs .nav-link.active {
        color: #2c3e50;
        background-color: #fff;
        border-bottom: none;
    }
    
    .custom-tabs .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 3px;
        background: linear-gradient(45deg, #3498db, #2980b9);
        transform: translateX(-50%);
        transition: width 0.3s ease;
    }
    
    .custom-tabs .nav-link.active::after {
        width: 80%;
    }
    
    .custom-tabs .nav-link:hover::after {
        width: 60%;
    }
    
    .tab-container {
        background-color: #fff;
    }
    
    /* Tab content styling */
    .tab-pane {
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
        position: absolute;
        width: 100%;
        left: 0;
    }
    
    .tab-pane.active,
    .tab-pane.show.active {
        display: block;
        opacity: 1;
        position: relative;
    }
    
    /* Card styling */
    .card {
        border-radius: 12px;
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .shadow-hover:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1), 0 6px 6px rgba(0, 0, 0, 0.05);
    }
    
    .card-header {
        padding: 0.75rem 1.25rem;
        border-bottom: none;
    }
    
    /* Form styling */
    .form-label {
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 0.6rem 0.75rem;
        transition: all 0.2s ease;
        background-color: #f9f9f9;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        background-color: #fff;
    }
    
    .form-text {
        font-size: 0.75rem;
        color: #95a5a6;
    }
    
    /* Table styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table thead th {
        background-color: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.85rem;
        border-bottom: 2px solid #e9ecef;
        padding: 0.75rem 1rem;
    }
    
    .table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        color: #34495e;
        font-size: 0.9rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
    }
    
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
        border-radius: 6px;
    }
    
    /* Animation for new data */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(52, 152, 219, 0); }
        100% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0); }
    }
    
    .new-data {
        animation: pulse 2s infinite;
    }
    
    /* Modal styling */
    .modal-content {
        border-radius: 12px;
        border: none;
        overflow: hidden;
    }
    
    .modal-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .modal-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
    }
</style> 