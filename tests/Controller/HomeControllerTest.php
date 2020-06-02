<?php

namespace App\Tests\Controller;

use App\Tests\Traits\LoginTrait;
use Doctrine\ORM\NonUniqueResultException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase
{
    use LoginTrait;
    use FixturesTrait;

    protected KernelBrowser $client;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();

        $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/SecurityUserFixturesTest.yaml'
        ]);
    }

    /**
     * @return void
     */
    public function testHomePageIsRestricted()
    {
        $this->client->request('GET', '/');
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
    }

    /**
     * @return void
     * @throws NonUniqueResultException
     */
    public function testLetAuthenticateUserAccessHomePage ()
    {
        $this->logIn($this->client, $this->getUser('user1@domain.com'));
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Home page');
    }

    /**
     * @return void
     */
    public function tearDown (): void
    {
        parent::tearDown();
    }
}
