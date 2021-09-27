<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $task = new Task();
        $task->setTitle($this->faker->word());
        $task->setContent($this->faker->paragraph(mt_rand(1, 3)));
        $task->setCreatedAt($this->faker->dateTime);
        $task->toggle($this->faker->numberBetween(0, 1));
        $task->setAuthor(null);

        $manager->persist($task);

        $this->createMany(Task::class, 100, function (Task $task) {
            $task->setTitle($this->faker->word());
            $task->setContent($this->faker->paragraph(mt_rand(1, 3)));
            $task->setCreatedAt($this->faker->dateTime);
            $task->toggle($this->faker->numberBetween(0, 1));
            $task->setAuthor($this->getRandomReference(User::class));
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixture::class];
    }
}
