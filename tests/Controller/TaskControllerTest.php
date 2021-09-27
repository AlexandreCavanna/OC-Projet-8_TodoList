<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private TaskRepository $taskRepository;
    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->taskRepository = self::getContainer()->get(TaskRepository::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    public function testListAction(): void
    {
        $this->client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
    }

    public function testCreateAction(): void
    {
        $user = $this->userRepository->findOneBy([]);
        $this->client->loginUser($user);
        $this->client->request('GET', '/tasks/create');

        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Ajouter', [
            'task[title]' => 'Ma super tâche !',
            'task[content]' => 'blablablablablabla',
        ]);

        $this->assertResponseRedirects('/tasks', 302);

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Superbe ! La tâche a été bien été ajoutée.', $crawler->filter('div.alert-success')->text());
    }

    public function testEditAction(): void
    {
        $this->client->request('GET', '/tasks/2/edit');

        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Modifier', [
            'task[title]' => 'Titre modifié',
            'task[content]' => 'Contenu modifié',
        ]);

        $this->assertResponseRedirects('/tasks', 302);

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Superbe ! La tâche a bien été modifiée.', $crawler->filter('div.alert-success')->text());
    }

    public function testToggleTaskAction(): void
    {
        $this->client->request('GET', '/tasks/2/toggle');

        $this->assertResponseRedirects('/tasks', 302);

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Superbe !', $crawler->filter('div.alert-success')->text());
    }

    public function testDeleteTaskLinkToUserAction(): void
    {
        $this->client->loginUser($this->taskRepository->findOneBy(['author' => '1'])->getAuthor());
        $this->client->request('DELETE', '/tasks/1/delete');

        $this->assertResponseRedirects('/tasks', 302);

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Superbe ! La tâche a bien été supprimée.', $crawler->filter('div.alert-success')->text());
    }

    public function testDeleteTaskAnonymousAction(): void
    {
        $this->client->loginUser($this->taskRepository->findOneBy(['author' => '1'])->getAuthor());
        $this->client->request('DELETE', '/tasks/2/delete');

        $this->assertResponseRedirects('/tasks', 302);

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Superbe ! La tâche a bien été supprimée.', $crawler->filter('div.alert-success')->text());
    }

    public function testDeleteTaskNotLogging(): void
    {
        $this->client->request('DELETE', '/tasks/1/delete');

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode(), 'Access Denied by controller annotation @IsGranted("MANAGE_TASK", task)');

    }

    public function testDeleteTaskNotLinkToUserWhoIsConnected(): void
    {
        $this->client->loginUser($this->taskRepository->findOneBy(['author' => '1'])->getAuthor());
        $this->client->request('DELETE', '/tasks/3/delete');

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode(), 'Access Denied by controller annotation @IsGranted("MANAGE_TASK", task)');

    }
}
