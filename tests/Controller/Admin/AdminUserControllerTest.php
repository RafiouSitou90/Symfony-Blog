<?php

namespace App\Tests\Controller\Admin;

use App\Tests\Traits\LoginTrait;
use Doctrine\ORM\NonUniqueResultException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminUserControllerTest extends WebTestCase
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
    public function testIndexIsRestricted()
    {
        $this->client->request('GET', '/admin/users');
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
    }

    /**
     * @return void
     * @throws NonUniqueResultException
     */
    public function testIndexAccessForbiddenForUser ()
    {
        $this->logIn($this->client, $this->getUser('user1@domain.com'));
        $this->client->request('GET', '/admin/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     * @throws NonUniqueResultException
     */
    public function testIndexAccessForbiddenForAdminUser ()
    {
        $this->logIn($this->client, $this->getUser('admin1@domain.com'));
        $this->client->request('GET', '/admin/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     * @throws NonUniqueResultException
     */
    public function testLetAuthenticateSuperAdminAccessIndex ()
    {
        $this->logIn($this->client, $this->getUser('super_admin1@domain.com'));
        $this->client->request('GET', '/admin/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return void
     */
    public function tearDown (): void
    {
        parent::tearDown();
    }
}
