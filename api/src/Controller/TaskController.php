<?php

namespace App\Controller;

use App\Models\Task;
use DateTime;

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

            $stmt->bindParam(':taskId', $taskId);
            $stmt->execute();

            $taskData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$taskData) {
                return ['error' => 'Task not found'];
            }

            $owners = json_decode($taskData['owners'], true);

            // Construct the Task with the array of owners
            $task = new Task(
                $taskData['task_id'],
                $taskData['title'],
                $taskData['created_at'],
                $taskData['finished_at'],
                $taskData['due_date'],
                $taskData['priority'],
                $owners
            );

            // Convert the task to an associative array
            $taskArray = [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'created_at' => $task->getCreatedAt(),
                'finished_at' => $task->getFinishedAt(),
                'due_date' => $task->getDueDate(),
                'priority' => $task->getPriority(),
                'owners' => $task->getOwners(),
            ];

            return $taskArray;
        } catch (\PDOException $e) {
            return ['error' => 'Error fetching task: ' . $e->getMessage()];
        }
    }

    public function createNewTask($title)
    {
        try {
            $createdAt = new DateTime();
            $formattedCreatedAt = $createdAt->format('Y-m-d H:i:s');

            $stmt = $this->pdo->prepare('
            INSERT INTO tasks (title, created_at) 
            VALUES (?, ?)
        ');
            $stmt->execute([
                $title,
                $formattedCreatedAt,
            ]);
        } catch (\PDOException $e) {
            // Lidar com erros, por exemplo, lançando uma exceção ou registrando o erro
        }
    }

    // Adicione este método à classe TaskController
public function updateTaskObject(Task $task, array $owners)
{
    try {
        $this->pdo->beginTransaction();

        // Atualize os detalhes da tarefa (title, due_date, priority)
        $stmtTask = $this->pdo->prepare('
            UPDATE tasks 
            SET title = :title, due_date = :dueDate, priority = :priority 
            WHERE id = :taskId
        ');

        $stmtTask->bindParam(':title', $task->getTitle());
        $stmtTask->bindParam(':dueDate', $task->getDueDate());
        $stmtTask->bindParam(':priority', $task->getPriority());
        $stmtTask->bindParam(':taskId', $task->getId());

        $stmtTask->execute();

        // Exclua os proprietários antigos
        $stmtDeleteOwners = $this->pdo->prepare('DELETE FROM task_users WHERE task_id = :taskId');
        $stmtDeleteOwners->bindParam(':taskId', $task->getId());
        $stmtDeleteOwners->execute();

        // Insira os novos proprietários
        $stmtInsertOwners = $this->pdo->prepare('INSERT INTO task_users (task_id, user_id) VALUES (:taskId, :userId)');
        $stmtInsertOwners->bindParam(':taskId', $task->getId());

        foreach ($owners as $owner) {
            $stmtInsertOwners->bindParam(':userId', $owner['user_id']);
            $stmtInsertOwners->execute();
        }

        $this->pdo->commit();

        return ['message' => 'Task updated successfully'];
    } catch (\PDOException $e) {
        $this->pdo->rollBack();
        return ['error' => 'Error updating task and owners: ' . $e->getMessage()];
    }
}

}
