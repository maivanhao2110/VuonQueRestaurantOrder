<?php
/**
 * Staff Routes
 * RESTful API Routes for staff endpoints
 */

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/StaffController.php';
require_once __DIR__ . '/../utils/Response.php';

$database = new Database();
$db = $database->getConnection();

$controller = new StaffController($db);
$requestMethod = $_SERVER['REQUEST_METHOD'];
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

switch ($path) {
	// ==================== Authentication ====================
	case '/api/staff/login':
		if ($requestMethod === 'POST') {
			$controller->login();
		} else {
			Response::error('Method not allowed');
		}
		break;

	// ==================== Orders ====================
	case '/api/staff/orders':
		if ($requestMethod === 'GET') {
			$controller->getAllOrders();
		} else {
			Response::error('Method not allowed');
		}
		break;

	// ==================== Menu ====================
	case '/api/staff/menu':
		if ($requestMethod === 'GET') {
			$controller->getMenuItems();
		} elseif ($requestMethod === 'POST') {
			$controller->createMenuItem();
		} else {
			Response::error('Method not allowed');
		}
		break;

	case '/api/staff/categories':
		if ($requestMethod === 'GET') {
			$controller->getCategories();
		} else {
			Response::error('Method not allowed');
		}
		break;

	default:
		// Handle dynamic routes

		// Order detail: /api/staff/orders/:id
		if (preg_match('#^/api/staff/orders/(\d+)$#', $path, $matches)) {
			$id = $matches[1];
			if ($requestMethod === 'GET') {
				$controller->getOrderDetail($id);
			} else {
				Response::error('Method not allowed');
			}
		}
		// Confirm order: /api/staff/orders/:id/confirm
		elseif (preg_match('#^/api/staff/orders/(\d+)/confirm$#', $path, $matches)) {
			$id = $matches[1];
			if ($requestMethod === 'PUT') {
				$controller->confirmOrder($id);
			} else {
				Response::error('Method not allowed');
			}
		}
		// Pay order: /api/staff/orders/:id/pay
		elseif (preg_match('#^/api/staff/orders/(\d+)/pay$#', $path, $matches)) {
			$id = $matches[1];
			if ($requestMethod === 'PUT') {
				$controller->payOrder($id);
			} else {
				Response::error('Method not allowed');
			}
		}
		// Update item status: /api/staff/order-items/:id/status
		elseif (preg_match('#^/api/staff/order-items/(\d+)/status$#', $path, $matches)) {
			$itemId = $matches[1];
			if ($requestMethod === 'PUT') {
				$controller->updateItemStatus($itemId);
			} else {
				Response::error('Method not allowed');
			}
		}
		// Menu item CRUD: /api/staff/menu/:id
		elseif (preg_match('#^/api/staff/menu/(\d+)$#', $path, $matches)) {
			$id = $matches[1];
			if ($requestMethod === 'GET') {
				$controller->getMenuItem($id);
			} elseif ($requestMethod === 'PUT') {
				$controller->updateMenuItem($id);
			} elseif ($requestMethod === 'DELETE') {
				$controller->deleteMenuItem($id);
			} else {
				Response::error('Method not allowed');
			}
		} else {
			Response::error('Endpoint không tồn tại');
		}
		break;
}
?>