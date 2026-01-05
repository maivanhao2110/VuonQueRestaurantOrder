<?php
/**
 * Menu Service
 */

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/MenuItem.php';

class MenuService {
    private $categoryModel;
    private $menuItemModel;

    public function __construct($db) {
        $this->categoryModel = new Category($db);
        $this->menuItemModel = new MenuItem($db);
    }

    public function getCategories() {
        return $this->categoryModel->getAll();
    }

    public function getMenuItems($categoryId = null) {
        return $this->menuItemModel->getAll($categoryId);
    }

    public function getMenuItem($id) {
        return $this->menuItemModel->getById($id);
    }
}
?>
