<?php
/**
 * Category Entity
 */

class Category {
    public $id;
    public $name;
    public $description;
    public $is_active;

    // Optional: Constructor to fill properties
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->description = $data['description'] ?? null;
            $this->is_active = $data['is_active'] ?? null;
        }
    }
}
?>
