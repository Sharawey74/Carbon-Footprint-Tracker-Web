<?php
// Initialize session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo isset($relativePath) ? $relativePath : ''; ?>css/styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1><?php echo SITE_NAME; ?></h1>
        </div>
        <nav>
            <ul>
                <li><a href="<?php echo isset($relativePath) ? $relativePath : ''; ?>index.php">Home</a></li>
                <li><a href="<?php echo isset($relativePath) ? $relativePath : ''; ?>dashboard/ceo_dashboard.php">CEO Dashboard</a></li>
            </ul>
        </nav>
    </header>
    <div class="main-content"> 