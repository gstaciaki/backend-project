<?php

use App\Controller\TaskController;

require_once 'Controller/TaskController.php';


function handleRequest($method, $uri)
{
    if ($method === 'GET' && $uri === '/api/rota') {
        return handleGetRoute();
    } elseif ($method === 'POST' && $uri === '/api/rota') {
        return handlePostRoute();
    } elseif ($method === 'GET' && $uri === '/api/test-database') {
        return handleTestDatabaseConnection();
    } elseif ($method === 'GET' && $uri === '/api/tasks') {
        return handleGetTasks();
    } elseif ($method === 'GET' && strpos($uri, '/api/tasks/') === 0) {
        $taskId = intval(substr($uri, 12));

        return handleGetTasks($taskId);
    } else {
        http_response_code(404);
        return ['erro' => 'Rota não encontrada'];
    }
}


function handleGetRoute()
{
    return ['mensagem' => 'Rota GET acessada com sucesso'];
}

function handlePostRoute()
{
    $dados = json_decode(file_get_contents("php://input"), true);
    return ['mensagem' => 'Rota POST acessada com sucesso', 'dados' => $dados];
}

function handleTestDatabaseConnection()
{
    require_once 'config.php';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $connected = $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);

        if ($connected) {
            return ['mensagem' => 'Conexão com o banco de dados estabelecida com sucesso'];
        } else {
            return ['erro' => 'Falha na conexão com o banco de dados'];
        }
    } catch (PDOException $e) {
        return ['erro' => 'Falha na conexão com o banco de dados: ' . $e->getMessage()];
    }
}

function handleGetTasks($taskId = null)
{
    // Configuração do banco de dados
    require_once 'config.php';

    try {
        // Conectar ao banco de dados
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Instanciar o TaskController com o objeto PDO
        $taskController = new TaskController($pdo);

        if ($taskId !== null) {
            // Obter uma tarefa específica pelo ID
            $task = $taskController->getTaskById($taskId);

            // Retornar a tarefa específica
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