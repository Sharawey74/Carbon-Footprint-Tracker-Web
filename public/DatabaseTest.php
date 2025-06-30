<?php
// Include the database configuration
require_once '../config/database.php';

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Data Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; max-width: 1200px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #2c3e50; }
        .section { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: #27ae60; font-weight: bold; }
        .error { color: #e74c3c; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #3498db; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .badge { display: inline-block; padding: 3px 7px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-draft { background-color: #f39c12; color: white; }
        .badge-progress { background-color: #3498db; color: white; }
        .badge-complete { background-color: #27ae60; color: white; }
    </style>
</head>
<body>
    <h1>Carbon Footprint Tracker - Database Test</h1>';

class DatabaseTest {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    private function executeQuery($sql) {
        $result = $this->conn->query($sql);
        if ($result === false) {
            throw new Exception("Query failed: " . $this->conn->errorInfo()[2]);
        }
        return $result;
    }

    public function testConnection() {
        $output = '<div class="section">';
        $output .= '<h2>Database Connection</h2>';
        
        try {
            // Test connection by running a simple query
            $this->conn->query("SELECT 1");
            $output .= '<p class="success">✓ Connection established successfully to ' . DB_NAME . '</p>';
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
        
        $output .= '</div>';
        return $output;
    }

    public function testCities() {
        $output = '<div class="section">';
        $output .= '<h2>Test 1: Cities</h2>';
        try {
            $result = $this->executeQuery("SELECT * FROM City ORDER BY CityID");
            if ($result->rowCount() > 0) {
                $output .= '<p class="success">✓ Found ' . $result->rowCount() . ' cities</p>';
                $output .= '<table><tr><th>ID</th><th>City Name</th></tr>';
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $output .= '<tr>';
                    $output .= '<td>' . htmlspecialchars($row['CityID']) . '</td>';
                    $output .= '<td>' . htmlspecialchars($row['CityName']) . '</td>';
                    $output .= '</tr>';
                }
                $output .= '</table>';
            } else {
                $output .= '<p class="error">✗ No cities found</p>';
            }
        } catch (Exception $e) {
            $output .= '<p class="error">✗ Error: ' . $e->getMessage() . '</p>';
        }
        $output .= '</div>';
        return $output;
    }

    public function testBranches() {
        $output = '<div class="section">';
        $output .= '<h2>Test 2: Branches</h2>';
        try {
            $result = $this->executeQuery("
                SELECT b.BranchID, c.CityName, b.Location, b.NumberOfEmployees
                FROM Branch b
                JOIN City c ON b.CityID = c.CityID
                ORDER BY c.CityName, b.Location
            ");
            
            if ($result->rowCount() > 0) {
                $output .= '<p class="success">✓ Found ' . $result->rowCount() . ' branches</p>';
                $output .= '<table>';
                $output .= '<tr><th>ID</th><th>City</th><th>Location</th><th>Employees</th></tr>';
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $output .= '<tr>';
                    $output .= '<td>' . htmlspecialchars($row['BranchID']) . '</td>';
                    $output .= '<td>' . htmlspecialchars($row['CityName']) . '</td>';
                    $output .= '<td>' . htmlspecialchars($row['Location']) . '</td>';
                    $output .= '<td>' . htmlspecialchars($row['NumberOfEmployees']) . '</td>';
                    $output .= '</tr>';
                }
                $output .= '</table>';
            } else {
                $output .= '<p class="error">✗ No branches found</p>';
            }
        } catch (Exception $e) {
            $output .= '<p class="error">✗ Error: ' . $e->getMessage() . '</p>';
        }
        $output .= '</div>';
        return $output;
    }
}

try {
    $conn = database::getConnection();
    $tester = new DatabaseTest($conn);
    
    echo $tester->testConnection();
    echo $tester->testCities();
    echo $tester->testBranches();

} catch (Exception $e) {
    echo '<div class="section error">';
    echo '<h2>Error</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}

echo '</body></html>';
?>