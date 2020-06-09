<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\Traits\AssertionErrors;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    use FixturesTrait;
    use AssertionErrors;

    /**
     * @return User
     */
    public function getUser (): User
    {
        return (new User())
            ->setUsername('user_name')
            ->setEmail('user_email@domain.com')
            ->setPassword('123456789')
            ->setFullName('First Last Name')
            ->setProfile(null)
        ;
    }

    /**
     * @return void
     */
    public function testValidUser ()
    {
        $this->assertHasErrors($this->getUser(), 0);
    }

    /**
     * @return void
     */
    public function testInvalidUserUsername ()
    {
        $this->assertHasErrors($this->getUser()->setUsername('user'), 1);
        $this->assertHasErrors($this->getUser()->setUsername(''), 2);
    }

    /**
     * @return void
     */
    public function testInvalidUserEmail ()
    {
        $this->assertHasErrors($this->getUser()->setEmail('email'), 1);
        $this->assertHasErrors($this->getUser()->setEmail(''), 1);
    }

    /**
     * @return void
     */
    public function testInvalidUserFullName ()
    {
        $this->assertHasErrors($this->getUser()->setFullName('name'), 1);
        $this->assertHasErrors($this->getUser()->setFullName(''), 2);
    }

    /**
     * @return void
     */
    public function testEntityWithDuplicatesUsername ()
    {
        $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/UserFixturesTest.yaml'
        ]);

        $this->assertHasErrors($this->getUser()->setUsername('username'), 1);
    }

    /**
     * @return void
     */
    public function testUserWithDuplicatesEmail ()
    {
        $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/UserFixturesTest.yaml'
        ]);
        $this->assertHasErrors($this->getUser()->setEmail('email@domain.com'), 1);
    }

}
