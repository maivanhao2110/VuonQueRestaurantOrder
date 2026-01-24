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
require_once __DIR__ . '/../core/Container.php';
require_once __DIR__ . '/../utils/Response.php';

// Services
require_once __DIR__ . '/../services/MenuService.php';
require_once __DIR__ . '/../services/StaffService.php';
require_once __DIR__ . '/../services/StatictisService.php';
require_once __DIR__ . '/../services/InvoiceService.php';

// Controllers
require_once __DIR__ . '/../controllers/admin/AdminCategoryController.php';
require_once __DIR__ . '/../controllers/admin/AdminMenuController.php';
require_once __DIR__ . '/../controllers/admin/AdminStaffController.php';
require_once __DIR__ . '/../controllers/admin/AdminStatisticsController.php';
require_once __DIR__ . '/../controllers/admin/AdminInvoiceController.php';

// Setup Container
$container = new Container();

$container->set('db', function($c) {
    return Database::getInstance()->getConnection();
});

$container->set('menuService', function($c) {
    return new MenuService($c->get('db'));
});

$container->set('staffService', function($c) {
    return new StaffService($c->get('db'));
});

$container->set('statService', function($c) {
    return new StatictisService($c->get('db'));
});

$container->set('invoiceService', function($c) {
    return new InvoiceService($c->get('db'));
});

$container->set('AdminCategoryController', function($c) {
    return new AdminCategoryController($c);
});

$container->set('AdminMenuController', function($c) {
    return new AdminMenuController($c);
});

$container->set('AdminStaffController', function($c) {
    return new AdminStaffController($c);
});

$container->set('AdminStatisticsController', function($c) {
    return new AdminStatisticsController($c);
});

$container->set('AdminInvoiceController', function($c) {
    return new AdminInvoiceController($c);
});

// Routing
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Extract path starting from /api/admin
$marker = '/api/admin';
$pos = strpos($requestUri, $marker);

if ($pos !== false) {
    $path = substr($requestUri, $pos);
} else {
    $path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
}

// Debug Router
file_put_contents(__DIR__ . '/debug_router.log', date('Y-m-d H:i:s') . " - Method: $requestMethod - Path: $path\n", FILE_APPEND);

// Simple Router Logic
switch ($path) {
	// ==================== Categories ====================
	case '/api/admin/categories':
		$controller = $container->get('AdminCategoryController');
		if ($requestMethod === 'GET') {
			$controller->listCategories();
		} elseif ($requestMethod === 'POST') {
			$controller->createCategory();
		} else {
			Response::error('Method not allowed');
		}
		break;

	// ==================== Menu Items ====================
	case '/api/admin/menu':
		$controller = $container->get('AdminMenuController');
		if ($requestMethod === 'GET') {
			$controller->listMenuItems();
		} elseif ($requestMethod === 'POST') {
			$controller->createMenuItem();
		} else {
			Response::error('Method not allowed');
		}
		break;

	// ==================== Staff ====================
	case '/api/admin/staff':
		$controller = $container->get('AdminStaffController');
		if ($requestMethod === 'GET') {
			$controller->listStaff();
		} elseif ($requestMethod === 'POST') {
			$controller->createStaff();
		} else {
			Response::error('Method not allowed');
		}
		break;

	// ==================== Statistics ====================
	case '/api/admin/statistics':
		$controller = $container->get('AdminStatisticsController');
		if ($requestMethod === 'GET') {
			$controller->getStatistics();
		} else {
			Response::error('Method not allowed');
		}
		break;

	// ==================== Invoices ====================
	case '/api/admin/invoices':
		$controller = $container->get('AdminInvoiceController');
		if ($requestMethod === 'GET') {
			$controller->listInvoices();
		} else {
			Response::error('Method not allowed');
		}
		break;

	default:
		// Dynamic routes
		if (preg_match('#^/api/admin/categories/(\d+)/status$#', $path, $matches)) {
			$controller = $container->get('AdminCategoryController');
			$id = $matches[1];
			if ($requestMethod === 'POST') {
				$controller->toggleCategoryStatus($id);
			} else {
				Response::error('Method not allowed');
			}
		} elseif (preg_match('#^/api/admin/categories/(\d+)$#', $path, $matches)) {
			$controller = $container->get('AdminCategoryController');
			$id = $matches[1];
			if ($requestMethod === 'PUT') {
				$controller->updateCategory($id);
			} elseif ($requestMethod === 'DELETE') {
				$controller->deleteCategory($id);
			} else {
				Response::error('Method not allowed');
			}
		} elseif (preg_match('#^/api/admin/menu/(\d+)$#', $path, $matches)) {
			$controller = $container->get('AdminMenuController');
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

		} elseif (preg_match('#^/api/admin/staff/(\d+)/status$#', $path, $matches)) {
			$controller = $container->get('AdminStaffController');
			$id = $matches[1];
			if ($requestMethod === 'PUT') {
				$controller->toggleStaffStatus($id);
			} else {
				Response::error('Method not allowed');
			}
		} elseif (preg_match('#^/api/admin/staff/(\d+)$#', $path, $matches)) {
			$controller = $container->get('AdminStaffController');
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
			$controller = $container->get('AdminInvoiceController');
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
