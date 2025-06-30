<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '123456789'); // 🔒 Change in production!
define('DB_NAME', 'carbon_footprint_tracker'); // Updated schema name

define('SITE_NAME', 'Carbon Footprint Tracker');
define('APP_URL', 'http://localhost:8000');
define('DEBUG_MODE', true);

// Session configuration
define('SESSION_NAME', 'carbon_tracker_session');
define('SESSION_LIFETIME', 86400); // 24 hours in seconds
define('DEFAULT_LANGUAGE', 'en');

// Path constants
define('ROOT_PATH', dirname(__DIR__));
define('VIEWS_PATH', ROOT_PATH . '/views');