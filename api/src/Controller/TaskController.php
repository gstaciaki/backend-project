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

                $owners = json_decode($taskData['owners'], true);

                $owners = array_filter($owners, function ($owner) {
                    return $owner['user_id'] !== null;
                });

                $task = new Task(
                    $taskData['task_id'],
                    $taskData['title'],
                    $taskData['created_at'],
                    $taskData['finished_at'],
                    $taskData['due_date'],
                    $taskData['priority'],
                    $owners
                );

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

            $owners = array_filter($owners, function ($owner) {
                return $owner['user_id'] !== null;
            });

            $task = new Task(
                $taskData['task_id'],
                $taskData['title'],
                $taskData['created_at'],
                $taskData['finished_at'],
                $taskData['due_date'],
                $taskData['priority'],
                $owners
            );

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

        }
    }

    public function updateTaskObject(Task $task, array $owners = null)
    {
        try {
            $this->pdo->beginTransaction();

            $stmtTask = $this->pdo->prepare('
                UPDATE tasks 
                SET title = :title, due_date = :dueDate, priority = :priority, finished_at = :finishedAt 
                WHERE id = :taskId');

            $taskId = $task->getId();

            $stmtTask->execute([
                ':title' => $task->getTitle(),
                ':dueDate' => $task->getDueDate(),
                ':priority' => $task->getPriority(),
                ':finishedAt' => $task->getFinishedAt(),
                ':taskId' => $taskId,
            ]);

            if ($owners !== null) {
                $stmtDeleteOwners = $this->pdo->prepare('DELETE FROM task_users WHERE task_id = :taskId');
                $stmtDeleteOwners->execute([':taskId' => $taskId]);

                $stmtInsertOwners = $this->pdo->prepare('INSERT INTO task_users (task_id, user_id) VALUES (:taskId, :userId)');
                $stmtInsertOwners->bindParam(':taskId', $taskId);

                foreach ($owners as $ownerId) {
                    $stmtInsertOwners->bindParam(':userId', $ownerId);
                    $stmtInsertOwners->execute();
                }
            }

            $this->pdo->commit();

            return ['message' => 'Task updated successfully'];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return ['error' => 'Error updating task and owners: ' . $e->getMessage()];
        }
    }


    public function deleteTask($taskId)
    {
        try {

            $stmt = $this->pdo->prepare('
            DELETE from tasks WHERE id = :taskId
        ');
            $stmt->bindParam(':taskId', $taskId);
            $stmt->execute();

            return $this->getTaskById($taskId);
        } catch (\PDOException $e) {
            return false;
        }
    }

}
