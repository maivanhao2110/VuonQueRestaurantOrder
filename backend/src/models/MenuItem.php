<?php
/**
 * MenuItem Entity
 */

class MenuItem {
    public $id;
    public $category_id;
    public $name;
    public $price;
    public $image_url;
    public $description;
    public $is_available;
    public $created_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->category_id = $data['category_id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->price = $data['price'] ?? null;
            $this->image_url = $data['image_url'] ?? null;
            $this->description = $data['description'] ?? null;
            $this->is_available = $data['is_available'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>
