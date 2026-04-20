<?php
// Simpan URL Absolut, atau DB disini
// Awal yang berada pada core/constant, dipindah semua ke config/config

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');
define('DB_NAME', 'auth');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base URL (without trailing slash)
// define('BASEURL', 'http://localhost/shidokan-web/admin/public');
// define('BASEURL', 'http://localhost/shidokan-web/user/public');
define('BASEURL', 'http://localhost/bikin-register-login-php');

// Define Paths
define('APP_PATH', dirname(__DIR__)); // /path/to/shidokan-web/app
define('PUBLIC_PATH', dirname(APP_PATH) . '/public'); // /path/to/shidokan-web/public

// No need to define section-specific URLs here
// They will be handled in the routing

?>