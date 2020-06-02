<?php

namespace App\Tests\Traits;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait LoginTrait
{
    /**
     * @return void
     *
     * @param KernelBrowser $client
     * @param User $user
     */
    public function logIn (KernelBrowser $client, User $user)
    {
        /** @var Session $session */
        $session = static::$container->get('session');

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }

    /**
     * @param string $username
     *
     * @return User
     * @throws NonUniqueResultException
     */
    private function getUser (string $username)
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::$container->get(UserRepository::class);
        return $userRepository->findUserByUsernameOrEmail($username);
    }
}
