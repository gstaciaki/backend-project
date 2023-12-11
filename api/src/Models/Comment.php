<?php

namespace App\Models;

class Comment {
    private $id;
    private $taskId;
    private $description;
    private $image;

    public function __construct($id, $taskId, $description, $image) {
        $this->id = $id;
        $this->taskId = $taskId;
        $this->description = $description;
        $this->image = $image;
    }

    public function getId() {
        return $this->id;
    }

    public function getTaskId() {
        return $this->taskId;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getImage() {
        return $this->image;
    }

    public function setTaskId($taskId) {
        $this->taskId = $taskId;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setImage($image) {
        $this->image = $image;
    }
}
