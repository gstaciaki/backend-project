<?php

namespace App\Models;

use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetId()
    {
        $task = new Task(1, 'Organizar o quarto');
        $this->assertEquals(1, $task->getId());
    }

    public function testGetTitle()
    {
        $task = new Task(1, 'Organizar o quarto');
        $this->assertEquals('Organizar o quarto', $task->getTitle());
    }

    public function testSetTitle()
    {
        $task = new Task(1, 'Organizar o quarto');
        $task->setTitle('Limpar a cozinha');
        $this->assertEquals('Limpar a cozinha', $task->getTitle());
    }

    public function testGetCreatedAt()
    {
        $task = new Task(1, 'Organizar o quarto');
        $this->assertInstanceOf(\DateTime::class, $task->getCreatedAt());
    }

}
