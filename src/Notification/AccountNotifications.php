<?php

namespace App\Notification;

use App\Entity\User;
use App\Traits\EmailAddressTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class AccountNotifications
{
    use EmailAddressTrait {
        EmailAddressTrait::__construct as private eatConstruct;
    }

    /**
     * @var Environment
     */
    private Environment $twig;
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    /**
     * AccountNotifications constructor.
     * @param Environment $twig
     * @param MailerInterface $mailer
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct (Environment $twig, MailerInterface $mailer, ParameterBagInterface $parameterBag)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->eatConstruct($parameterBag);
    }

    /**
     * @param User $user
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notifyUserForRegistration (User $user)
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->getNoReplyAddress(), 'Symfony Blog'))
            ->to((string)$user->getEmail())
            ->subject('Activation of your account')
            ->htmlTemplate('emails/activation_email.html.twig')
            ->context([
                'token' => $user->getToken(),
                'id' => $user->getId()
            ])
        ;

        $this->mailer->send($email);
    }

    /**
     * @param User $user
     *
     * @return void
     * @throws TransportExceptionInterface
     */
    public function notifyUserForActivation (User $user)
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->getNoReplyAddress(), 'Symfony Blog'))
            ->to((string)$user->getEmail())
            ->subject('Your account has been activated')
            ->htmlTemplate('emails/activation_success_email.html.twig')
        ;

        $this->mailer->send($email);
    }
}
