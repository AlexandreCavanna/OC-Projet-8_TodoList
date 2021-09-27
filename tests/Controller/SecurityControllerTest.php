<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testLoginRedirect(): void
    {
        $this->client->loginUser($this->userRepository->findOneBy([]));
        $this->client->request('GET', '/login');

        $this->assertResponseRedirects('/', 302);
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @dataProvider providerUrls
     */
    public function testLoginWithRoleUser($url): void
    {
        $this->client->loginUser($this->userRepository->findOneByRole('ROLE_USER'));
        $this->client->request('GET', $url);

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @dataProvider providerUrls
     */
    public function testLoginWithRoleAdmin($url): void
    {
        $this->client->loginUser($this->userRepository->findOneByRole('ROLE_ADMIN'));
        $this->client->request('GET', $url);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testLoginWithValidCredentials(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            '_username' => 'user',
            '_password' => 'password',
        ]);

        $this->assertResponseRedirects('/', 302);

        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Se déconnecter', $crawler->filter('a.pull-right.btn.btn-danger')->text());
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            '_username' => 'ndqphdhop',
            '_password' => 'fdshdhh',
        ]);

        $this->assertResponseRedirects('/login', 302);
        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Invalid credentials.', $crawler->filter('div.alert-danger')->text());
    }

    public function providerUrls(): Generator
    {
        yield 'users' => ['/users'];
        yield 'user_create' => ['/users/create'];
        yield 'user_edit' => ['/users/1/edit'];
    }

    public function testListActionNotLogging(): void
    {
        $this->client->request('GET', '/users');

        $this->assertResponseRedirects('/login', 302);
    }
}
