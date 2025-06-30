<?php
/**
 * Database Test Script for Carbon Tracker
 * 
 * This script tests the database connection and retrieves sample data
 * from key tables to verify proper configuration and data access.
 */

// Load necessary files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

// Set content type to plain text for CLI-friendly output
header('Content-Type: text/plain');

echo "===== CARBON TRACKER DATABASE TEST =====\n\n";

// Test 1: Database Connection
echo "TEST 1: Database Connection\n";
echo "----------------------------\n";
$db = database::getConnection();

if (!$db) {
    echo "❌ FAILED: Could not connect to the database.\n";
    echo "   Please check your database settings in config.php:\n";
    echo "   - Host: " . DB_HOST . "\n";
    echo "   - Database: " . DB_NAME . "\n";
    echo "   - User: " . DB_USER . "\n";
    die();
} else {
    echo "✅ SUCCESS: Database connection established.\n\n";
}

// Test 2: Verify Users Table
echo "TEST 2: Users Table\n";
echo "------------------\n";
try {
    $query = "SELECT COUNT(*) as total FROM User";
    $stmt = $db->query($query);
    $result = $stmt->fetch();
    
    echo "Total users in database: " . $result['total'] . "\n";
    
    if ($result['total'] > 0) {
        echo "✅ SUCCESS: Users table exists and contains data.\n\n";
        
        // Sample user data
        $query = "SELECT UserID, UserName, UserRole, UserEmail FROM User LIMIT 3";
        $stmt = $db->query($query);
        $users = $stmt->fetchAll();
        
        echo "Sample users:\n";
        echo str_repeat('-', 80) . "\n";
        echo sprintf("%-5s | %-20s | %-12s | %-30s\n", "ID", "Name", "Role", "Email");
        echo str_repeat('-', 80) . "\n";
        
        foreach ($users as $user) {
            echo sprintf("%-5s | %-20s | %-12s | %-30s\n", 
                $user['UserID'], 
                $user['UserName'], 
                $user['UserRole'], 
                $user['UserEmail']
            );
        }
        echo "\n";
    } else {
        echo "⚠️ WARNING: Users table exists but contains no data.\n\n";
    }
} catch (Exception $e) {
    echo "❌ FAILED: Error accessing Users table: " . $e->getMessage() . "\n\n";
}

// Test 3: Verify Branches Table
echo "TEST 3: Branches Table\n";
echo "---------------------\n";
try {
    $query = "SELECT COUNT(*) as total FROM Branch";
    $stmt = $db->query($query);
    $result = $stmt->fetch();
    
    echo "Total branches in database: " . $result['total'] . "\n";
    
    if ($result['total'] > 0) {
        echo "✅ SUCCESS: Branch table exists and contains data.\n\n";
        
        // Sample branch data with city name
        $query = "SELECT b.BranchID, b.Location, c.CityName, b.NumberOfEmployees 
                  FROM Branch b 
                  JOIN City c ON b.CityID = c.CityID 
                  LIMIT 3";
        $stmt = $db->query($query);
        $branches = $stmt->fetchAll();
        
        echo "Sample branches:\n";
        echo str_repeat('-', 70) . "\n";
        echo sprintf("%-5s | %-15s | %-15s | %-20s\n", "ID", "City", "Location", "# Employees");
        echo str_repeat('-', 70) . "\n";
        
        foreach ($branches as $branch) {
            echo sprintf("%-5s | %-15s | %-15s | %-20s\n", 
                $branch['BranchID'], 
                $branch['CityName'], 
                $branch['Location'], 
                $branch['NumberOfEmployees']
            );
        }
        echo "\n";
    } else {
        echo "⚠️ WARNING: Branch table exists but contains no data.\n\n";
    }
} catch (Exception $e) {
    echo "❌ FAILED: Error accessing Branch table: " . $e->getMessage() . "\n\n";
}

// Test 4: Verify Carbon Data Tables
echo "TEST 4: Carbon Data Tables\n";
echo "------------------------\n";

// Test CoffeeProduction table
try {
    $query = "SELECT COUNT(*) as total FROM CoffeeProduction";
    $stmt = $db->query($query);
    $result = $stmt->fetch();
    
    echo "Coffee Production records: " . $result['total'] . "\n";
    
    if ($result['total'] > 0) {
        echo "✅ SUCCESS: CoffeeProduction table exists and contains data.\n";
        
        // Test sample record
        $query = "SELECT ProductionID, BranchID, CoffeeType, ProductType, 
                 ProductionQuantitiesOfCoffee_KG, Pr_CarbonEmissions_KG 
                 FROM CoffeeProduction LIMIT 1";
        $stmt = $db->query($query);
        $production = $stmt->fetch();
        
        if ($production) {
            echo "   Sample Production Record ID: " . $production['ProductionID'] . "\n";
            echo "   Carbon Emissions: " . $production['Pr_CarbonEmissions_KG'] . " kg\n";
        }
    } else {
        echo "⚠️ WARNING: CoffeeProduction table exists but contains no data.\n";
    }
} catch (Exception $e) {
    echo "❌ FAILED: Error accessing CoffeeProduction table: " . $e->getMessage() . "\n";
}

echo "\n";

// Test CoffeePackaging table
try {
    $query = "SELECT COUNT(*) as total FROM CoffeePackaging";
    $stmt = $db->query($query);
    $result = $stmt->fetch();
    
    echo "Coffee Packaging records: " . $result['total'] . "\n";
    
    if ($result['total'] > 0) {
        echo "✅ SUCCESS: CoffeePackaging table exists and contains data.\n";
    } else {
        echo "⚠️ WARNING: CoffeePackaging table exists but contains no data.\n";
    }
} catch (Exception $e) {
    echo "❌ FAILED: Error accessing CoffeePackaging table: " . $e->getMessage() . "\n";
}

echo "\n";

// Test CoffeeDistribution table
try {
    $query = "SELECT COUNT(*) as total FROM CoffeeDistribution";
    $stmt = $db->query($query);
    $result = $stmt->fetch();
    
    echo "Coffee Distribution records: " . $result['total'] . "\n";
    
    if ($result['total'] > 0) {
        echo "✅ SUCCESS: CoffeeDistribution table exists and contains data.\n";
    } else {
        echo "⚠️ WARNING: CoffeeDistribution table exists but contains no data.\n";
    }
} catch (Exception $e) {
    echo "❌ FAILED: Error accessing CoffeeDistribution table: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Verify Carbon Calculations
echo "TEST 5: Carbon Calculations\n";
echo "-------------------------\n";
try {
    // Get a sample of calculated emissions
    $query = "SELECT 
                (SELECT SUM(Pr_CarbonEmissions_KG) FROM CoffeeProduction) as production,
                (SELECT SUM(Pa_CarbonEmissions_KG) FROM CoffeePackaging) as packaging,
                (SELECT SUM(V_CarbonEmissions_Kg) FROM CoffeeDistribution) as distribution";
    $stmt = $db->query($query);
    $emissions = $stmt->fetch();
    
    echo "Total Carbon Emissions Summary:\n";
    echo "- Production: " . ($emissions['production'] ?? 0) . " kg\n";
    echo "- Packaging: " . ($emissions['packaging'] ?? 0) . " kg\n";
    echo "- Distribution: " . ($emissions['distribution'] ?? 0) . " kg\n";
    echo "- Total: " . (($emissions['production'] ?? 0) + ($emissions['packaging'] ?? 0) + ($emissions['distribution'] ?? 0)) . " kg\n\n";
    
    echo "✅ SUCCESS: Carbon calculations can be performed.\n\n";
} catch (Exception $e) {
    echo "❌ FAILED: Error calculating emissions: " . $e->getMessage() . "\n\n";
}

echo "===== DATABASE TEST COMPLETED =====\n";
echo "Run this script anytime you need to verify database connectivity and data access.";
