<?php

namespace App\Tests\Controller;

use App\Controller\TaskController;
use PHPUnit\Framework\TestCase;
use App\Models\Task;

class TaskControllerTest extends TestCase
{
    private function configurePdoMock($queryReturnValue)
    {
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $pdoStatementMock->expects($this->any())
            ->method('fetchAll')
            ->willReturn($queryReturnValue);

        $pdoMock = $this->createMock(\PDO::class);
        $pdoMock->expects($this->any())
            ->method('query')
            ->willReturn($pdoStatementMock);

        return $pdoMock;
    }

    private function configureTaskController($pdoMock)
    {
        return new TaskController($pdoMock);
    }

    public function testGetAllTasks()
    {
        $taskData = [
            ['id' => 1, 'title' => 'Task 1'],
            ['id' => 2, 'title' => 'Task 2'],
        ];

        $pdoMock = $this->configurePdoMock($taskData);
        $taskController = $this->configureTaskController($pdoMock);

        $tasks = $taskController->getAllTasks();

        $this->assertIsArray($tasks);
        $this->assertCount(count($taskData), $tasks);

        foreach ($tasks as $index => $task) {
            $this->assertInstanceOf(Task::class, $task);
            $this->assertEquals($taskData[$index]['id'], $task->getId());
            $this->assertEquals($taskData[$index]['title'], $task->getTitle());
        }
    }

    public function testCreateNewTask()
    {
        $task = new Task(1, 'New Task');

        $pdoMock = $this->createMock(\PDO::class);

        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $taskController = $this->configureTaskController($pdoMock);

        $taskController->createNewTask($task);
    }



}
