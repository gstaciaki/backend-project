<?php

namespace App\Models;

use DateTime;

class Task
{
    private $id;
    private $title;
    private $createdAt;
    private $finishedAt;
    private $dueDate;
    private $priority;
    private $owners;

    public function __construct($id, $title, $createdAt = null, $finishedAt = null, $dueDate = null, $priority = null, $owners = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->finishedAt = $finishedAt;
        $this->dueDate = $dueDate;
        $this->priority = $priority;
        $this->owners = $owners;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    public function setFinishedAt($finishedAt)
    {
        $this->finishedAt = $finishedAt;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }

    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getOwners()
    {
        return $this->owners ?? [];
    }

    public function setOwners($owners)
    {
        if (is_array($owners)) {
            foreach ($owners as $owner) {
                $this->setNewOwner($owner);
            }
        } else {
            $this->owners = [];
        }
    }

    public function setNewOwner($owner)
    {
        $this->owners[] = $owner;
    }
}
