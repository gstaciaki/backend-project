<?php

use App\Controller\TaskController;
use App\Models\Task;

require_once 'Controller/TaskController.php';


function handleRequest($method, $uri)
{
    if ($method === 'GET' && $uri === '/api/tasks') {
        return handleGetTasks();
    }
    if ($method === 'POST' && $uri === '/api/tasks') {
        return handlePostTask();
    }
    if ($method === 'GET' && strpos($uri, '/api/tasks/') === 0) {
        $taskId = intval(substr($uri, 11));
        return handleGetTasks($taskId);
    }
    if ($method === 'PUT' && strpos($uri, '/api/tasks/') === 0) {
        $taskId = intval(substr($uri, 11));
        return handleUpdateTask($taskId);
    }

    http_response_code(404);
    return ['erro' => 'Rota não encontrada'];

}

function handlePostTask()
{
    $dados = json_decode(file_get_contents("php://input"), true);
    require_once 'config.php';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $taskController = new TaskController($pdo);

        $tasks = $taskController->createNewTask($dados['title']);

        // Retornar as tarefas
        return ['mensagem' => 'Task inserida com sucesso', 'dados' => $dados];
    } catch (PDOException $e) {
        // Lidar com erros de conexão com o banco de dados
        return ['erro' => 'Falha na conexão com o banco de dados: ' . $e->getMessage()];
    }
}

function handleUpdateTask($taskId)
{
    $dados = json_decode(file_get_contents("php://input"), true);
    require_once 'config.php';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $taskController = new TaskController($pdo);

        // Obter a tarefa existente
        $existingTaskData = $taskController->getTaskById($taskId);

        if (isset($existingTaskData['error'])) {
            http_response_code(404);
            return ['error' => 'Tarefa não encontrada'];
        }

        // Criar uma instância de Task com os dados existentes
        $existingTask = new Task(
            $existingTaskData['id'],
            $existingTaskData['title'],
            $existingTaskData['created_at'],
            $existingTaskData['finished_at'],
            $existingTaskData['due_date'],
            $existingTaskData['priority'],
            $existingTaskData['owners']
        );

        // Atualizar apenas as propriedades não nulas do objeto Task
        foreach ($dados as $key => $value) {
            if (!is_null($value)) {
                $existingTask->{$key} = $value;
            }
        }

        // Atualizar a tarefa no banco de dados
        $result = $taskController->updateTaskObject($existingTask);

        if (isset($result['error'])) {
            http_response_code(500);
        }

        return $result;
    } catch (PDOException $e) {
        // Lidar com erros de conexão com o banco de dados
        return ['erro' => 'Falha na conexão com o banco de dados: ' . $e->getMessage()];
    }
}


function handleGetTasks($taskId = null)
{
    require_once 'config.php';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $taskController = new TaskController($pdo);

        if ($taskId !== null) {
            $task = $taskController->getTaskById($taskId);

            return $task;
        }

        // Obter todas as tarefas
        $tasks = $taskController->getAllTasks();

        // Retornar as tarefas
        return $tasks;
    } catch (PDOException $e) {
        // Lidar com erros de conexão com o banco de dados
        return ['erro' => 'Falha na conexão com o banco de dados: ' . $e->getMessage()];
    }
}