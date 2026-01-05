<?php
/**
 * Customer Routes
 * RESTful API Routes for customer endpoints
 */

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/CustomerController.php';
require_once __DIR__ . '/../utils/Response.php';

$database = new Database();
$db = $database->getConnection();

// Create controller instance
$controller = new CustomerController($db);

// Get request method and path
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Use PATH_INFO which Apache provides automatically
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

// Route handling
switch ($path) {
    // Menu routes
    case '/api/customer/categories':
        if ($requestMethod === 'GET') {
            $controller->getCategories();
        } else {
            Response::error('Method not allowed');
        }
        break;

    case '/api/customer/menu':
        if ($requestMethod === 'GET') {
            $controller->getMenu();
        } else {
            Response::error('Method not allowed');
        }
        break;

    // Order routes
    case '/api/customer/orders':
        if ($requestMethod === 'POST') {
            $controller->createOrder();
        } elseif ($requestMethod === 'GET') {
            $controller->getOrders();
        } else {
            Response::error('Method not allowed');
        }
        break;

    default:
        // Handle dynamic routes like /api/customer/menu/:id
        if (preg_match('#^/api/customer/menu/(\d+)$#', $path, $matches)) {
            if ($requestMethod === 'GET') {
                $controller->getMenuItem($matches[1]);
            } else {
                Response::error('Method not allowed');
            }
        } elseif (preg_match('#^/api/customer/orders/(\d+)$#', $path, $matches)) {
            if ($requestMethod === 'GET') {
                $controller->getOrderDetail($matches[1]);
            } else {
                Response::error('Method not allowed');
            }
        } else {
            Response::error('Endpoint không tồn tại');
        }
        break;
}
?>
