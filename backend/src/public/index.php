<?php
/**
 * Main API Entry Point
 * Routes requests to appropriate handlers
 */

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../utils/Response.php';

// Get the request path
$requestUri = $_SERVER['REQUEST_URI'];

// Parse the path
$path = parse_url($requestUri, PHP_URL_PATH);

// Route to appropriate handler
if (strpos($path, '/api/customer') !== false) {
    require_once __DIR__ . '/../routes/customer.php';
} elseif (strpos($path, '/api/staff') !== false) {
    require_once __DIR__ . '/../routes/staff.php';
} elseif (strpos($path, '/api/admin') !== false) {
    require_once __DIR__ . '/../routes/admin.php';
} elseif (strpos($path, '/api') !== false) {
    Response::success('VuonQueRestaurant API', [
        'version' => '1.0.0',
        'endpoints' => [
            'customer' => '/api/customer',
            'staff' => '/api/staff',
            'admin' => '/api/admin'
        ]
    ]);
} else {
    Response::error('API endpoint not found');
}
?>
