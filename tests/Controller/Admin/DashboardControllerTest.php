<?php

namespace App\Tests\Controller\Admin;

use App\Tests\Traits\LoginTrait;
use Doctrine\ORM\NonUniqueResultException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DashboardControllerTest extends WebTestCase
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
            dirname(__DIR__). '/../DataFixtures/SecurityUserFixturesTest.yaml'
        ]);
    }

    /**
     * @return void
     */
    public function testDashboardIsRestricted ()
    {
        $this->client->request('GET', '/admin');
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
    }

    /**
     * @return void
     * @throws NonUniqueResultException
     */
    public function testLetAuthenticateAdminAccessDashboard ()
    {
        $this->logIn($this->client, $this->getUser('admin1@domain.com'));
        $this->client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin Dashboard');
    }

    /**
     * @return void
     * @throws NonUniqueResultException
     */
    public function testBlockAuthenticateAdminAccessDashboard ()
    {
        $this->logIn($this->client, $this->getUser('user1@domain.com'));
        $this->client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     * @throws NonUniqueResultException
     */
    public function testLetAuthenticateSuperAdminDashboard ()
    {
        $this->logIn($this->client, $this->getUser('super_admin1@domain.com'));
        $this->client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('title', 'Admin Dashboard');
    }

    /**
     * @return void
     */
    public function tearDown (): void
    {
        parent::tearDown();
    }
}
