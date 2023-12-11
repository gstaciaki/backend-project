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
            $task = $taskController->getTaskById($taskId);

            return $task;
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