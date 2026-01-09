<?php
/**
 * Admin Routes
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
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../utils/Response.php';

$database = new Database();
$db = $database->getConnection();

$controller = new AdminController($db);
$requestMethod = $_SERVER['REQUEST_METHOD'];
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

switch ($path) {
	// Category
	case '/api/admin/categories':
		if ($requestMethod === 'GET') {
			$controller->listCategories();
		} elseif ($requestMethod === 'POST') {
			$controller->createCategory();
		} else {
			Response::error('Method not allowed');
		}
		break;

	// Menu items
	case '/api/admin/menu':
		if ($requestMethod === 'GET') {
			$controller->listMenuItems();
		} elseif ($requestMethod === 'POST') {
			$controller->createMenuItem();
		} else {
			Response::error('Method not allowed');
		}
		break;

	// Staff
	case '/api/admin/staff':
		if ($requestMethod === 'GET') {
			$controller->listStaff();
		} elseif ($requestMethod === 'POST') {
			$controller->createStaff();
		} else {
			Response::error('Method not allowed');
		}
		break;

	// Statistics
	case '/api/admin/statistics':
		if ($requestMethod === 'GET') {
			$controller->getStatistics();
		} else {
			Response::error('Method not allowed');
		}
		break;

	// Invoices
	case '/api/admin/invoices':
		if ($requestMethod === 'GET') {
			$controller->listInvoices();
		} else {
			Response::error('Method not allowed');
		}
		break;

	default:
		if (preg_match('#^/api/admin/categories/(\d+)$#', $path, $matches)) {
			$id = $matches[1];
			if ($requestMethod === 'PUT') {
				$controller->updateCategory($id);
			} elseif ($requestMethod === 'DELETE') {
				$controller->deleteCategory($id);
			} else {
				Response::error('Method not allowed');
			}
		} elseif (preg_match('#^/api/admin/menu/(\d+)$#', $path, $matches)) {
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
		} elseif (preg_match('#^/api/admin/staff/(\d+)$#', $path, $matches)) {
			$id = $matches[1];
			if ($requestMethod === 'GET') {
				$controller->getStaff($id);
			} elseif ($requestMethod === 'PUT') {
				$controller->updateStaff($id);
			} elseif ($requestMethod === 'DELETE') {
				$controller->deleteStaff($id);
			} else {
				Response::error('Method not allowed');
			}
		} elseif (preg_match('#^/api/admin/invoices/(\d+)$#', $path, $matches)) {
			$id = $matches[1];
			if ($requestMethod === 'GET') {
				$controller->getInvoice($id);
			} else {
				Response::error('Method not allowed');
			}
		} else {
			Response::error('Endpoint không tồn tại');
		}
		break;
}

?>
