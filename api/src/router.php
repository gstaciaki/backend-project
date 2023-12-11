<?php

use App\Controller\TaskController;
use App\Models\Task;
use App\Controller\UserController;
use App\Models\User;
use App\Controller\CommentController;
use App\Models\Comment;

require_once 'Controller/TaskController.php';
require_once 'Controller/UserController.php';
require_once 'Controller/CommentController.php';


function handleRequest($method, $uri)
{
    require_once 'config.php';

    $taskController = new TaskController($pdo);
    $userController = new UserController($pdo);
    $commentController = new CommentController($pdo);

    if ($method === 'POST' && $uri === '/api/login') {
        return handlePostLogin($userController);
    }

    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        return ['error' => 'Authorization header is missing'];
    }

    $authorizationHeader = $headers['Authorization'];

    if (!$userController->verifySession($authorizationHeader)) {
        http_response_code(403);
        return ['error' => 'Not authorized'];
    }

    if ($method === 'GET' && preg_match('/\/tasks\/(\d+)\/comments/', $uri, $matches)) {
        $taskId = intval($matches[1]);
        return handleGetTaskComments($commentController, $taskId);
    }
    if ($method === 'POST' && preg_match('/\/tasks\/(\d+)\/comments/', $uri, $matches)) {
        $taskId = intval($matches[1]);
        return handlePostComment($commentController, $taskId);
    }
    if ($method === 'PUT' && preg_match('/\/tasks\/(\d+)\/comments\/(\d+)/', $uri, $matches)) {
        $taskId = intval($matches[1]);
        $commentId = intval($matches[2]);
        return handleUpdateComment($commentController, $taskId, $commentId);
    }

    if ($method === 'DELETE' && preg_match('/\/tasks\/(\d+)\/comments\/(\d+)/', $uri, $matches)) {
        $taskId = intval($matches[1]);
        $commentId = intval($matches[2]);
        return handleDeleteComment($commentController, $commentId);
    }
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
    if ($method === 'PUT' && preg_match('/\/tasks\/(\d+)/', $uri, $matches)) {
        $taskId = intval($matches[1]);
        return handleUpdateTask($taskController, $taskId);
    }
    if ($method === 'DELETE' && preg_match('/\/tasks\/(\d+)/', $uri, $matches)) {
        $taskId = intval($matches[1]);
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
    $data = json_decode(file_get_contents("php://input"), true);

    try {
        $taskController->createNewTask($data['title']);

        return ['mensagem' => 'Task inserida com sucesso'];
    } catch (PDOException $e) {
        return ['erro' => 'Database connection failed: ' . $e->getMessage()];
    }
}

function handleUpdateTask($taskController, $taskId)
{
    $data = json_decode(file_get_contents("php://input"), true);

    try {
        $existingTaskData = $taskController->getTaskById($taskId);

        if (isset($existingTaskData['error'])) {
            http_response_code(404);
            return ['error' => 'Tarefa não encontrada'];
        }

        $task = new Task(
            $taskId,
            $data['title'] ?? $existingTaskData['title'],
            $data['created_at'] ?? $existingTaskData['created_at'],
            $data['finished_at'] ?? $existingTaskData['finished_at'],
            $data['due_date'] ?? $existingTaskData['due_date'],
            $data['priority'] ?? $existingTaskData['priority'],
        );

        $owners = $data['owners'] ?? null;

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
        return ['error' => 'User not found'];
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
        return ['error' => 'User not found'];
    }

    if ($userController->deleteUser($userId))
        return $existingUserData;
}

function handleGetTaskComments($commentController, $taskId)
{
    try {
        return $commentController->getTaskComments($taskId);
    } catch (PDOException $e) {
        return ['error' => 'Database connection failed: ' . $e->getMessage()];
    }
}

function handlePostComment($commentController, $taskId)
{
    $data = json_decode(file_get_contents("php://input"), true);

    $commentDescription = $data["description"];
    $commentImage = $data["image"];

    $result = $commentController->createNewComment($taskId, $commentDescription, $commentImage);

    if (isset($result['error'])) {
        http_response_code(500);
    }

    return $result;
}

function handleGetComment($commentController, $commentId)
{
    $comment = $commentController->getCommentById($commentId);

    if (isset($comment['error'])) {
        http_response_code(404);
    }

    return $comment;
}

function handleUpdateComment($commentController, $taskId, $commentId)
{
    $data = json_decode(file_get_contents("php://input"), true);

    $existingCommentData = $commentController->getCommentById($commentId);

    if (isset($existingCommentData['error'])) {
        http_response_code(404);
        return ['error' => 'Comment not found'];
    }

    $comment = new Comment(
        $commentId,
        $taskId,
        $data['description'] ?? $existingCommentData['description'],
        $data['image'] ?? $existingCommentData['image']
    );

    $result = $commentController->updateCommentObject($comment);

    if (isset($result['error'])) {
        http_response_code(500);
    }

    return $result;
}

function handleDeleteComment($commentController, $commentId)
{
    $existingCommentData = $commentController->getCommentById($commentId);

    if (isset($existingCommentData['error'])) {
        http_response_code(404);
        return ['error' => 'Comment not found'];
    }

    $commentController->deleteComment($commentId);

    return $existingCommentData;
}

function handlePostLogin($userController)
{
    $data = json_decode(file_get_contents("php://input"), true);

    $email = $data["email"];
    $password = $data["password"];

    return $userController->executeLogin($email, $password);
}