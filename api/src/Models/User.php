<?php

namespace App\Models;

class User
{
    private $id;
    private $username;
    private $password;
    private $email;
    private $createdAt;
    private $fullName;
    private $isAdmin;

    public function __construct($id, $username, $password, $email, $createdAt, $fullName, $isAdmin)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->createdAt = $createdAt;
        $this->fullName = $fullName;
        $this->isAdmin = $isAdmin;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function isAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setAdmin(?bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }
}
