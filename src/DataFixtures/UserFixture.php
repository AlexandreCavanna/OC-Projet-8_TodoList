<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends BaseFixture
{
    private const ROLE_USER = 'ROLE_USER';

    private const ROLE_ADMIN = 'ROLE_ADMIN';

    private UserPasswordHasherInterface $passwordEncoder;

    /**
     * UpdatePassword constructor.
     */
    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'password'));
        $admin->setEmail('admin@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);

        $user = new User();
        $user->setUsername('user');
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'password'));
        $user->setEmail('user@gmail.com');
        $user->setRoles(['ROLE_USER']);

        $task = new Task();
        $task->setTitle('Titre de la tâche');
        $task->setContent('Description de la tâche');
        $task->setAuthor($admin);

        $manager->persist($admin);
        $manager->persist($user);
        $manager->persist($task);

        $this->createMany(User::class, 50, function (User $user) {
            $user->setEmail($this->faker->email());
            $user->setUsername($this->faker->userName());
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'password'));
            $user->setRoles($this->faker->randomElement([[self::ROLE_USER], [self::ROLE_ADMIN]]));
        });

        $manager->flush();
    }
}
