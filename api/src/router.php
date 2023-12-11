<?php

use App\Controller\TaskController;
use App\Models\Task;
use App\Controller\UserController;
use App\Models\User;

require_once 'Controller/TaskController.php';
require_once 'Controller/UserController.php';


function handleRequest($method, $uri)
{
    require_once 'config.php';

    $taskController = new TaskController($pdo);
    $userController = new UserController($pdo);

    if ($method === 'GET' && $uri === '/api/tasks') {
        return handleGetTasks($taskController);
    }
    if ($method === 'POST' && $uri === '/api/tasks') {
        return handlePostTask($taskController);
    }
    if ($method === 'GET' && strpos($uri, '/api/tasks/') === 0) {
        $taskId = intval(substr($uri, 11));
        return handleGetTasks($taskController, $taskId);
    }
    if ($method === 'PUT' && strpos($uri, '/api/tasks/') === 0) {
        $taskId = intval(substr($uri, 11));
        return handleUpdateTask($taskController, $taskId);
    }
    if ($method === 'DELETE' && strpos($uri, '/api/tasks/') === 0) {
        $taskId = intval(substr($uri, 11));
        return handleDeleteTask($taskController, $taskId);
    }
    if ($method === 'GET' && $uri === '/api/users') {
        return handleGetUsers($userController);
    }
    if ($method === 'POST' && $uri === '/api/users') {
        return handlePostUser($userController);
    }
    if ($method === 'GET' && strpos($uri, '/api/users/') === 0) {
        $userId = intval(substr($uri, 11));
        return handleGetUser($userController, $userId);
    }
    if ($method === 'PUT' && strpos($uri, '/api/users/') === 0) {
        $userId = intval(substr($uri, 11));
        return handleUpdateUser($userController, $userId);
    }
    if ($method === 'DELETE' && strpos($uri, '/api/users/') === 0) {
        $userId = intval(substr($uri, 11));
        return handleDeleteUser($userController, $userId);
    }

    http_response_code(404);
    return ['error' => 'Route not found'];
}

function handlePostTask($taskController)
{
    $dados = json_decode(file_get_contents("php://input"), true);

    try {
        $taskController->createNewTask($dados['title']);

        return ['mensagem' => 'Task inserida com sucesso'];
    } catch (PDOException $e) {
        return ['erro' => 'Database connection failed: ' . $e->getMessage()];
    }
}

function handleUpdateTask($taskController, $taskId)
{
    $dados = json_decode(file_get_contents("php://input"), true);

    try {
        $existingTaskData = $taskController->getTaskById($taskId);

        if (isset($existingTaskData['error'])) {
            http_response_code(404);
            return ['error' => 'Tarefa não encontrada'];
        }

        $task = new Task(
            $taskId,
            $dados['title'] ?? $existingTaskData['title'],
            $dados['created_at'] ?? $existingTaskData['created_at'],
            $dados['finished_at'] ?? $existingTaskData['finished_at'],
            $dados['due_date'] ?? $existingTaskData['due_date'],
            $dados['priority'] ?? $existingTaskData['priority'],
        );

        $owners = $dados['owners'] ?? null;

        $result = $taskController->updateTaskObject($task, $owners);

        if (isset($result['error'])) {
            http_response_code(500);
        }

        return $result;
    } catch (PDOException $e) {
        return ['erro' => 'Database connection failed: ' . $e->getMessage()];
    }
}

function handleGetTasks($taskController, $taskId = null)
{
    try {
        if ($taskId !== null) {
            return $taskController->getTaskById($taskId);
        }

        return $taskController->getAllTasks();
    } catch (PDOException $e) {
        return ['erro' => 'Database connection failed: ' . $e->getMessage()];
    }
}

function handleDeleteTask($taskController, $taskId)
{
    try {
        $existingTaskData = $taskController->getTaskById($taskId);

        if (isset($existingTaskData['error'])) {
            http_response_code(404);
            return ['error' => 'Task not found'];
        }

        $taskController->deleteTask($taskId);

        return $existingTaskData;
    } catch (PDOException $e) {
        return ['erro' => 'Database connection failed: ' . $e->getMessage()];
    }
}

function handleGetUsers($userController)
{
    return $userController->getAllUsers();
}

function handlePostUser($userController)
{
    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'] ?? null;
    $password = $data['password'] ?? null;
    $email = $data['email'] ?? null;
    $fullName = $data['fullName'] ?? null;
    $isAdmin = $data['admin'] ?? null;

    $result = $userController->createNewUser($username, $password, $email, $fullName, $isAdmin);

    if (isset($result['error'])) {
        http_response_code(500);
    }

    return $result;
}

function handleGetUser($userController, $userId)
{
    $user = $userController->getUserById($userId);

    if (isset($user['error'])) {
        http_response_code(404);
    }

    return $user;
}

function handleUpdateUser($userController, $userId)
{
    $data = json_decode(file_get_contents("php://input"), true);

    $existingUserData = $userController->getUserById($userId);

    if (isset($existingUserData['error'])) {
        http_response_code(404);
        return ['error' => 'Usuário não encontrado'];
    }

    $user = new User(
        $userId,
        $data['username'] ?? $existingUserData['username'],
        $data['password'] ?? $existingUserData['password'],
        $data['email'] ?? $existingUserData['email'],
        $data['created_at'] ?? $existingUserData['created_at'],
        $data['full_name'] ?? $existingUserData['full_name'],
        $data['is_admin'] ?? $existingUserData['is_admin']
    );

    $result = $userController->updateUserObject($user);

    if (isset($result['error'])) {
        http_response_code(500);
    }

    return $result;
}

function handleDeleteUser($userController, $userId)
{
    $existingUserData = $userController->getUserById($userId);

    if (isset($existingUserData['error'])) {
        http_response_code(404);
        return ['error' => 'Usuário não encontrado'];
    }

    if ($userController->deleteUser($userId))
        return $existingUserData;
}