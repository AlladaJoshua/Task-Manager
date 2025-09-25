<?php 
class Task {
    public $id;
    public $description;
    public $status;
    public $createdAt;
    public $updatedAt;

    public function __construct($id, $description, $status = "todo", $createdAt = null, $updatedAt = null) {
        $this->id = $id;
        $this->description = $description;
        $this->status = $status;
        $this->createdAt = $createdAt ?? date("Y-m-d H:i:s");
        $this->updatedAt = $updatedAt ?? date("Y-m-d H:i:s");
    }
}