<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetSetTitle()
    {
        $task = new Task();
        $task->setTitle('Test title');
        $this->assertEquals('Test title', $task->getTitle());
    }

    public function testGetSetContent()
    {
        $task = new Task();
        $task->setContent('Test content');
        $this->assertEquals('Test content', $task->getContent());
    }

    public function testGetSetCreatedAt()
    {
        $task = new Task();
        $task->setCreatedAt(new DateTime());
        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt());
    }

    public function testGetSetAuthor()
    {
        $task = new Task();
        $task->setAuthor(new User());
        $this->assertInstanceOf(User::class, $task->getAuthor());
    }

    public function testIsDoneToggle()
    {
        $task = new Task();
        $task->toggle(true);
        $this->assertEquals(true, $task->isDone());
    }
}
