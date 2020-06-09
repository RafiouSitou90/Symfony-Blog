<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Notification\AccountNotifications;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationController
 * @package App\Controller
 *
 * @Route(name="app_account_")
 */
class RegistrationController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var AccountNotifications
     */
    private AccountNotifications $accountNotifications;

    /**
     * RegistrationController constructor.
     * @param EntityManagerInterface $manager
     * @param UserRepository $userRepository
     * @param MailerInterface $mailer
     * @param AccountNotifications $accountNotifications
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct (
        EntityManagerInterface $manager,
        UserRepository $userRepository,
        MailerInterface $mailer,
        AccountNotifications $accountNotifications,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->passwordEncoder = $passwordEncoder;
        $this->accountNotifications = $accountNotifications;
    }

    /**
     * @Route("/register", name="register", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function register (Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData()));

            $this->manager->persist($user);
            $this->manager->flush();

            $this->accountNotifications->notifyUserForRegistration($user);
            $this->addFlash(
                'success',
                'Your account has been created successfully. A confirmation email has been sent to you. 
                Please check your email and confirm it.'
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/activation/{token}/{id}", name="activation", methods={"GET"})
     *
     * @param string $id
     * @param string $token
     * @return RedirectResponse
     * @throws TransportExceptionInterface
     */
    public function activation (string $token, string $id)
    {
        $user = $this->userRepository->findOneBy(['id' => $id, 'token' => $token]);

        if (!$user) {
            throw $this->createNotFoundException("Oops, User not found.");
        }

        $user->setIsActive(true);
        $user->setToken(null);
        $this->manager->flush();

        $this->accountNotifications->notifyUserForActivation($user);
        $this->addFlash('success', 'Your account has been activated successfully.');

        return $this->redirectToRoute('app_home');
    }
}
