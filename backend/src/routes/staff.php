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
// Standardized Path Extraction
// We explicitly look for '/api/staff' in the request URI to ensure we get the full correct path
// regardless of how the server populates PATH_INFO.
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$marker = '/api/staff';
$pos = strpos($requestUri, $marker);

if ($pos !== false) {
	$path = substr($requestUri, $pos);
} else {
	// Fallback only if marker not found (unlikely if routed here)
	$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
}

// DEBUG: Log the calculated path
file_put_contents(__DIR__ . '/debug_path.log', date('Y-m-d H:i:s') . " - URI: $requestUri - Path: $path\n", FILE_APPEND);

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

		// Add order item: /api/staff/orders/:id/items (Moved to top priority)
		// We use both regex and a fallback robust check
		if (preg_match('#^/api/staff/orders/(\d+)/items/?$#i', $path, $matches)) {
			$id = $matches[1];
			if ($requestMethod === 'POST') {
				$controller->addOrderItem($id);
			} else {
				Response::error('Method not allowed');
			}
		}
		// Fallback for Add Item if regex fails (e.g. slight path variations)
		elseif ($requestMethod === 'POST' && strpos($path, '/items') !== false && preg_match('/orders\/(\d+)\/items/', $path, $m)) {
			$controller->addOrderItem($m[1]);
		}
		// Order detail: /api/staff/orders/:id
		elseif (preg_match('#^/api/staff/orders/(\d+)$#', $path, $matches)) {
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
		// Cancel order: /api/staff/orders/:id/cancel
		elseif (preg_match('#^/api/staff/orders/(\d+)/cancel$#', $path, $matches)) {
			$id = $matches[1];
			if ($requestMethod === 'PUT') {
				$controller->cancelOrder($id);
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
		// Update item quantity: /api/staff/order-items/:id
		elseif (preg_match('#^/api/staff/order-items/(\d+)$#', $path, $matches)) {
			$itemId = $matches[1];
			if ($requestMethod === 'PUT') {
				$controller->updateOrderItemQuantity($itemId);
			} elseif ($requestMethod === 'DELETE') {
				$controller->deleteOrderItem($itemId);
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