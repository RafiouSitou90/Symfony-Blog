<?php

namespace App\Tests\Controller\Admin;

use App\Tests\Traits\LoginTrait;
use Doctrine\ORM\NonUniqueResultException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminCategoriesControllerTest extends WebTestCase
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
        $this->client->request('GET', '/admin/categories');
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
    }

    /**
     * @return void
     * @throws NonUniqueResultException
     */
    public function testIndexAccessForbidden ()
    {
        $this->logIn($this->client, $this->getUser('user1@domain.com'));
        $this->client->request('GET', '/admin/categories');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @return void
     * @throws NonUniqueResultException
     */
    public function testLetAuthenticateAdminAccessIndex ()
    {
        $this->logIn($this->client, $this->getUser('admin1@domain.com'));
        $this->client->request('GET', '/admin/categories');
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
