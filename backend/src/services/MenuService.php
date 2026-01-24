<?php
/**
 * Menu Service
 */

require_once __DIR__ . '/../repositories/CategoryRepository.php';
require_once __DIR__ . '/../repositories/MenuItemRepository.php';

class MenuService {
    private $categoryRepo;
    private $menuItemRepo;

    public function __construct($db) {
        // In the future, these should be injected via DI container
        $this->categoryRepo = new CategoryRepository($db);
        $this->menuItemRepo = new MenuItemRepository($db);
    }

    public function getCategories() {
        return $this->categoryRepo->getAllActive();
    }

    public function getCategoriesAdmin($includeInactive = true) {
        return $this->categoryRepo->getAllAdmin($includeInactive);
    }

    public function getCategory($id) {
        return $this->categoryRepo->getById($id);
    }

    public function createCategory($name, $description = null, $isActive = 1) {
        return $this->categoryRepo->create($name, $description, $isActive);
    }

    public function updateCategory($id, $name, $description = null, $isActive = 1) {
        return $this->categoryRepo->update($id, $name, $description, $isActive);
    }

    public function deleteCategory($id) {
        return $this->categoryRepo->delete($id);
    }

    public function toggleCategoryStatus($id, $isActive) {
        return $this->categoryRepo->setActive($id, $isActive);
    }

    public function getMenuItems($categoryId = null) {
        return $this->menuItemRepo->getAll($categoryId);
    }

    public function getMenuItemsAdmin($categoryId = null, $includeUnavailable = true) {
        return $this->menuItemRepo->getAllAdmin($categoryId, $includeUnavailable);
    }

    public function getMenuItem($id) {
        return $this->menuItemRepo->getById($id);
    }

    public function createMenuItem($categoryId, $name, $price, $imageUrl = null, $description = null, $isAvailable = 1) {
        return $this->menuItemRepo->create($categoryId, $name, $price, $imageUrl, $description, $isAvailable);
    }

    public function updateMenuItem($id, $categoryId, $name, $price, $imageUrl = null, $description = null, $isAvailable = 1) {
        return $this->menuItemRepo->update($id, $categoryId, $name, $price, $imageUrl, $description, $isAvailable);
    }

    public function deleteMenuItem($id) {
        // Soft delete
        return $this->menuItemRepo->setAvailable($id, 0);
    }
}
?>
