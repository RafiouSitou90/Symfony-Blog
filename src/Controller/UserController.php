<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\UserUpdateFormType;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @package App\Controller
 *
 * @Route("/user/account", name="app_user_account_")
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var ProfileRepository
     */
    private ProfileRepository $profileRepository;

    /**
     * UserController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $manager
     * @param ProfileRepository $profileRepository
     * @param UserRepository $userRepository
     */
    public function __construct (
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $manager,
        ProfileRepository $profileRepository,
        UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
    }
    /**
     * @Route("", name="index", methods={"GET"})
     * @return Response
     */
    public function index()
    {
        return $this->render('user/index.html.twig', []);
    }

    /**
     * @Route("/edit", name="edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit (Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getProfile()) {
                /** @var Profile $profile */
                $profile = $user->getProfile();
                if (!$profile->getAvatarFile() && !$profile->getAvatarName()) {
                    $user->setProfile(null);
                }
            }

            if ($user->getProfile()) {
                /** @var Profile $profile */
                $profile = $user->getProfile();
                if ($profile->getAvatarFile() !== null && $profile->getAvatarName() !== null) {
                    $profileTampon = $profile->getAvatarFile();
                    $user->setProfile(null);

                    $newProfile = new Profile();
                    $newProfile->setAvatarFile($profileTampon);
                    $this->manager->persist($newProfile);
                    $user->setProfile($newProfile);
                }
            }

            $this->manager->flush();
            return $this->redirectToRoute('app_user_account_edit');
        }

        return $this->render('user/edit/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/change-password", name="change_password", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function changePassword (Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form->get('newPassword')->getData()));

            $this->manager->flush();

            return $this->redirectToRoute('app_logout');
        }

        return $this->render('user/edit/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
