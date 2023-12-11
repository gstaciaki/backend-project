<?php

namespace App\Controller;

use App\Models\User;
use DateTime;

class UserController
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllUsers(): array
    {
        try {
            $stmt = $this->pdo->query('
                SELECT * FROM users;
            ');

            $usersData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $users = [];
            foreach ($usersData as $userData) {
                $user = new User(
                    $userData['id'],
                    $userData['username'],
                    $userData['password'],
                    $userData['email'],
                    $userData['created_at'],
                    $userData['full_name'],
                    $userData['is_admin']
                );

                $userArray = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'password' => $user->getPassword(),
                    'email' => $user->getEmail(),
                    'created_at' => $user->getCreatedAt(),
                    'full_name' => $user->getFullName(),
                    'is_admin' => $user->isAdmin(),
                ];

                $users[] = $userArray;
            }

            return ['users' => $users];
        } catch (\PDOException $e) {
            return ['error' => 'Erro ao obter os usuários: ' . $e->getMessage()];
        }
    }

    public function getUserById($userId)
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT * FROM users WHERE id = :userId;
            ');

            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$userData) {
                return ['error' => 'User not found'];
            }

            $user = new User(
                $userData['id'],
                $userData['username'],
                $userData['password'],
                $userData['email'],
                $userData['created_at'],
                $userData['full_name'],
                $userData['is_admin']
            );

            $userArray = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'password' => $user->getPassword(),
                'email' => $user->getEmail(),
                'created_at' => $user->getCreatedAt(),
                'full_name' => $user->getFullName(),
                'is_admin' => $user->isAdmin(),
            ];

            return $userArray;
        } catch (\PDOException $e) {
            return ['error' => 'Error fetching user: ' . $e->getMessage()];
        }
    }

    public function createNewUser($username, $password, $email, $fullName, $isAdmin)
    {
        try {
            $createdAt = new DateTime();
            $formattedCreatedAt = $createdAt->format('Y-m-d H:i:s');

            $stmt = $this->pdo->prepare('
                INSERT INTO users (username, password, email, created_at, full_name, is_admin) 
                VALUES (?, ?, ?, ?, ?, ?);
            ');

            $stmt->execute([$username, $password, $email, $formattedCreatedAt, $fullName, $isAdmin]);
        } catch (\PDOException $e) {
            return ['error' => 'Error creating user: ' . $e->getMessage()];
        }
    }

    public function updateUserObject(User $user)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE users 
                SET username = :username, password = :password, 
                    email = :email, created_at = :createdAt, 
                    full_name = :fullName, is_admin = :isAdmin 
                WHERE id = :userId;
            ');

            $stmt->bindParam(':username', $user->getUsername());
            $stmt->bindParam(':password', $user->getPassword());
            $stmt->bindParam(':email', $user->getEmail());
            $stmt->bindParam(':createdAt', $user->getCreatedAt());
            $stmt->bindParam(':fullName', $user->getFullName());
            $stmt->bindParam(':isAdmin', $user->isAdmin());
            $stmt->bindParam(':userId', $user->getId());

            $stmt->execute();

            return ['message' => 'User updated successfully'];
        } catch (\PDOException $e) {
            return ['error' => 'Error updating user: ' . $e->getMessage()];
        }
    }

    public function deleteUser($userId)
    {
        try {
            $stmt = $this->pdo->prepare('
                DELETE FROM users WHERE id = :userId;
            ');

            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            return $this->getUserById($userId);
        } catch (\PDOException $e) {
            return ['error' => 'Error deleting user: ' . $e->getMessage()];
        }
    }
}
