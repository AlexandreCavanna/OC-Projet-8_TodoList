<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    public function testListAction(): void
    {
        $this->client->loginUser($this->userRepository->findOneBy(['username' => 'admin']));
        $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
    }

    public function testCreateAction(): void
    {
        $this->client->loginUser($this->userRepository->findOneBy(['username' => 'admin']));
        $this->client->request('GET', '/users/create');

        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Ajouter', [
            'user[username]' => 'test',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'test@gmail.com',
            'user[roles]' => 'ROLE_ADMIN',
        ]);

        $this->assertResponseRedirects('/users', 302);

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Superbe ! L\'utilisateur a bien été ajouté.', $crawler->filter('div.alert-success')->text());
    }

    public function testEditAction(): void
    {
        $this->client->loginUser($this->userRepository->findOneBy(['username' => 'admin']));
        $this->client->request('GET', '/users/17/edit');

        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Modifier', [
            'user[username]' => 'toto',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'toto@gmail.com',
            'user[roles]' => 'ROLE_ADMIN',
        ]);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Superbe ! L\'utilisateur a bien été modifié', $crawler->filter('div.alert-success')->text());
    }
}
