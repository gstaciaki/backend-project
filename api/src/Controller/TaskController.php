<?php

namespace App\Controller;

use App\Models\Task;

class TaskController
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function testDatabaseConnection()
    {
        $connected = $this->isDatabaseConnected();

        if ($connected) {
            return new JsonResponse(['message' => 'Conexão com o banco de dados estabelecida com sucesso.'], 200);
        } else {
            return new JsonResponse(['message' => 'Falha na conexão com o banco de dados.'], 500);
        }
    }


    public function getAllTasks(): array
    {
        try {
            $stmt = $this->pdo->query('
            SELECT
                t.id AS task_id,
                t.title,
                t.created_at,
                t.finished_at,
                t.due_date,
                t.priority,
                JSON_ARRAYAGG(
                    JSON_OBJECT("user_id", u.id, "username", u.username, "email", u.email, "is_admin", u.admin)
                ) AS owners
            FROM        
                tasks t
            LEFT JOIN task_users tu ON t.id = tu.task_id
            LEFT JOIN user u ON tu.user_id = u.id
            GROUP BY
                t.id, t.title, t.created_at, t.finished_at, t.due_date, t.priority;');

            $tasksData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $tasks = [];
            foreach ($tasksData as $taskData) {
                // Decodificar a string JSON da coluna 'owners' para obter um array de usuários
                $owners = json_decode($taskData['owners'], true);

                // Construir a Task com o array de owners
                $task = new Task(
                    $taskData['task_id'],
                    $taskData['title'],
                    $taskData['created_at'],
                    $taskData['finished_at'],
                    $taskData['due_date'],
                    $taskData['priority'],
                    $owners
                );

                // Converta a tarefa para um array associativo
                $taskArray = [
                    'id' => $task->getId(),
                    'title' => $task->getTitle(),
                    'created_at' => $task->getCreatedAt(),
                    'finished_at' => $task->getFinishedAt(),
                    'due_date' => $task->getDueDate(),
                    'priority' => $task->getPriority(),
                    'owners' => $task->getOwners(),
                ];

                $tasks[] = $taskArray;
            }

            return ['tasks' => $tasks];
        } catch (\PDOException $e) {
            return ['error' => 'Erro ao obter as tarefas: ' . $e->getMessage()];
        }
    }

    public function getTaskById($taskId)
    {
        try {
            $stmt = $this->pdo->prepare('
            SELECT
                t.id AS task_id,
                t.title,
                t.created_at,
                t.finished_at,
                t.due_date,
                t.priority,
                JSON_ARRAYAGG(
                    JSON_OBJECT("user_id", u.id, "username", u.username, "email", u.email, "is_admin", u.admin)
                ) AS owners
            FROM        
                tasks t
            LEFT JOIN task_users tu ON t.id = tu.task_id
            LEFT JOIN user u ON tu.user_id = u.id
            WHERE
                t.id = :taskId
            GROUP BY
                t.id, t.title, t.created_at, t.finished_at, t.due_date, t.priority
        ');

            $taskData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $owners = json_decode($taskData['owners'], true);

            // Construir a Task com o array de owners
            $task = new Task(
                $taskData['task_id'],
                $taskData['title'],
                $taskData['created_at'],
                $taskData['finished_at'],
                $taskData['due_date'],
                $taskData['priority'],
                $owners
            );

            // Converta a tarefa para um array associativo
            $taskArray = [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'created_at' => $task->getCreatedAt(),
                'finished_at' => $task->getFinishedAt(),
                'due_date' => $task->getDueDate(),
                'priority' => $task->getPriority(),
                'owners' => $task->getOwners(),
            ];

            return [$taskArray];
        } catch (\PDOException $e) {
            return ['error' => 'Erro ao obter as tarefas: ' . $e->getMessage()];
        }
    }




    public function createNewTask(Task $task)
    {
        try {

            $stmt = $this->pdo->prepare('
                INSERT INTO tasks (title, created_at, finished_at, due_date, priority) 
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $task->getTitle(),
                $task->getCreatedAt(),
                $task->getFinishedAt(),
                $task->getDueDate(),
                $task->getPriority()
            ]);
        } catch (\PDOException $e) {

        }
    }
}
