<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SecurityControllerTest extends WebTestCase
{
    protected KernelBrowser $client;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testLoginPage()
    {
        $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Sign in');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    /**
     * @return void
     */
    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Sign in');
        $form = $crawler->selectButton('Sign in')->form([
            'username' => 'fake_email@domain.com',
            'password' => 'fake_password'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('h1', 'Sign in');
    }

    /**
     * @return void
     */
    public function testSuccessfulLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        /** METHOD 1 */
        $form = $crawler->selectButton('Sign in')->form([
            'username' => 'admin@domain.com',
            'password' => '123456789'
        ]);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseHasHeader('Location', '/');
        $this->assertResponseRedirects('', Response::HTTP_FOUND);
        $this->client->followRedirect();
        /** ************************* **/

        /** METHOD 2 */

        /** @var CsrfTokenManagerInterface $tokenManager */
        $tokenManager = static::$container->get(CsrfTokenManagerInterface::class);
        $csrfToken = $tokenManager->getToken('authenticate');

        $this->client->request('POST', '/login', [
            'username' => 'admin@domain.com',
            'password' => '123456789',
            '_csrf_token' => $csrfToken
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseHasHeader('Location', '/');
        $this->assertResponseRedirects('', Response::HTTP_FOUND);
        $this->client->followRedirect();
        /** ************************* **/
    }

    /**
     * @return void
     */
    public function tearDown (): void
    {
        parent::tearDown();
    }

}
