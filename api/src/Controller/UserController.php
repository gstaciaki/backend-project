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
                SELECT * FROM user;
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
                    $userData['admin']
                );

                $userArray = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'password' => $user->getPassword(),
                    'email' => $user->getEmail(),
                    'created_at' => $user->getCreatedAt(),
                    'full_name' => $user->getFullName(),
                    'admin' => $user->isAdmin(),
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
                SELECT * FROM user WHERE id = :userId;
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
                $userData['admin']
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
        $props = [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'fullName' => $fullName,
            'isAdmin' => $isAdmin
        ];

        foreach ($props as $key => $value) {
            if ($value === null) {
                return ['error' => $key . ' is missing'];
            }
        }
        $createdAt = new DateTime();
        $formattedCreatedAt = $createdAt->format('Y-m-d H:i:s');
        try {


            $stmt = $this->pdo->prepare('
                INSERT INTO user (username, password, email, created_at, full_name, admin) 
                VALUES (?, ?, ?, ?, ?, ?);
            ');

            $stmt->execute([
                $username,
                md5($password),
                $email,
                $formattedCreatedAt,
                $fullName,
                $isAdmin ? true : false
            ]);

            return ['message' => 'User succefully created'];
        } catch (\PDOException $e) {
            return ['error' => 'Error creating user: ' . $e->getMessage()];
        }
    }

    public function updateUserObject(User $user)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE user 
                SET username = :username, password = :password, 
                    email = :email, created_at = :createdAt, 
                    full_name = :fullName, admin = :isAdmin 
                WHERE id = :userId;
            ');

            $stmt->execute([
                ':username' => $user->getUsername(),
                ':password' => $user->getPassword(),
                ':email' => $user->getEmail(),
                ':createdAt' => $user->getCreatedAt(),
                ':fullName' => $user->getFullName(),
                ':isAdmin' => $user->isAdmin(),
                ':userId' => $user->getId(),
            ]);

            return ['message' => 'User updated successfully'];
        } catch (\PDOException $e) {
            return ['error' => 'Error updating user: ' . $e->getMessage()];
        }
    }

    public function deleteUser($userId)
    {
        try {
            $stmt = $this->pdo->prepare('
                DELETE FROM user WHERE id = :userId;
            ');

            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            return true;
        } catch (\PDOException $e) {
            return ['error' => 'Error deleting user: ' . $e->getMessage()];
        }
    }

    public function executeLogin($email, $password)
    {
        $password = md5($password);
        try {
            $stmt = $this->pdo->prepare('
            SELECT * FROM user WHERE email = :email;
        ');

            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$userData) {
                return ['error' => 'User not found'];
            }

            if ($password !== $userData['password']) {
                return ['error' => 'Invalid password'];
            }

            return ['session' => md5($email . $password)];
        } catch (\PDOException $e) {
            return ['error' => 'Error during login: ' . $e->getMessage()];
        }
    }

    public function verifySession($session)
    {
        try {
            $stmt = $this->pdo->prepare('
            SELECT email, password FROM user;
        ');
            $stmt->execute();

            $arrayUsers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($arrayUsers as $user) {
                if (md5($user['email'] . $user['password']) == $session)
                    return true;
            }

            return false;
        } catch (\PDOException $e) {
            return ['error' => 'Error during session verify: ' . $e->getMessage()];
        }
    }
}