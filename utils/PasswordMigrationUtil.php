<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/PasswordHasher.php';

// Connect to database
$db = database::getConnection();

// Get all users
$select = $db->query("SELECT UserID, Password FROM User");
$update = $db->prepare("UPDATE User SET Password = ? WHERE UserID = ?");

// Process each user
while ($row = $select->fetch(PDO::FETCH_OBJ)) {
    // Hash the current password using the updated method
    $hashedPassword = PasswordHasher::hashPassword($row->Password);
    
    // Update the database with hashed password
    $update->execute([$hashedPassword, $row->UserID]);
    echo "Migrated user {$row->UserID}\n";
}

echo "Password migration completed successfully!\n";
?>