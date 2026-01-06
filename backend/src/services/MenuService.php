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

    public function getCategoriesAdmin($includeInactive = true) {
        return $this->categoryModel->getAllAdmin($includeInactive);
    }

    public function getCategory($id) {
        return $this->categoryModel->getById($id);
    }

    public function createCategory($name, $description = null, $isActive = 1) {
        return $this->categoryModel->create($name, $description, $isActive);
    }

    public function updateCategory($id, $name, $description = null, $isActive = 1) {
        return $this->categoryModel->update($id, $name, $description, $isActive);
    }

    public function deleteCategory($id) {
        // Soft delete
        return $this->categoryModel->setActive($id, 0);
    }

    public function getMenuItems($categoryId = null) {
        return $this->menuItemModel->getAll($categoryId);
    }

    public function getMenuItemsAdmin($categoryId = null, $includeUnavailable = true) {
        return $this->menuItemModel->getAllAdmin($categoryId, $includeUnavailable);
    }

    public function getMenuItem($id) {
        return $this->menuItemModel->getById($id);
    }

    public function createMenuItem($categoryId, $name, $price, $imageUrl = null, $description = null, $isAvailable = 1) {
        return $this->menuItemModel->create($categoryId, $name, $price, $imageUrl, $description, $isAvailable);
    }

    public function updateMenuItem($id, $categoryId, $name, $price, $imageUrl = null, $description = null, $isAvailable = 1) {
        return $this->menuItemModel->update($id, $categoryId, $name, $price, $imageUrl, $description, $isAvailable);
    }

    public function deleteMenuItem($id) {
        // Soft delete
        return $this->menuItemModel->setAvailable($id, 0);
    }
}
?>
