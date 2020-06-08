<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminUserController
 * @package App\Controller\Admin
 *
 * @Route("/admin/users", name="admin_users_")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class AdminUserController extends AbstractController
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
     * AdminUserController constructor.
     * @param EntityManagerInterface $manager
     * @param UserRepository $userRepository
     */
    public function __construct (
        EntityManagerInterface $manager,
        UserRepository $userRepository)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index ()
    {
        return $this->render('admin/users/index.html.twig', [
            'users' => $this->userRepository->findBy([], ['username' => 'ASC'])
        ]);
    }

    /**
     * @Route("/{id}/activate", name="activate", methods={"POST"})
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function activateAccount (Request $request, User $user)
    {
        if (!$this->isCsrfTokenValid('active', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_users_index');
        }
        $user
            ->setIsActive(true)
            ->setToken(null)
            ->setIsDeleted(false);

        $this->manager->flush();

        $this->addFlash('success', 'User account activated successfully');

        return $this->redirectToRoute('admin_users_index');
    }

    /**
     * @Route("/{id}/deactivate", name="deactivate", methods={"POST"})
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws Exception
     */
    public function deActivateAccount (Request $request, User $user)
    {
        if (!$this->isCsrfTokenValid('deactivate', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_users_index');
        }
        $user
            ->setIsActive(false)
            ->setToken(User::generateActivationToken())
            ->setIsDeleted(false);

        $this->manager->flush();

        $this->addFlash('success', 'User account deactivated successfully');

        return $this->redirectToRoute('admin_users_index');
    }

    /**
     * @Route("/{id}/partial-delete", name="partial_delete", methods={"POST"})
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function deletePartiallyAccount (Request $request, User $user)
    {
        if (!$this->isCsrfTokenValid('delete-partially', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_users_index');
        }
        $user->setIsDeleted(true);

        $this->manager->flush();

        $this->addFlash('success', 'User account deleted partially successfully');

        return $this->redirectToRoute('admin_users_index');
    }

    /**
     * @Route("/{id}/change-roles", name="roles", methods={"POST"})
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function changeAccountRoles (Request $request, User $user)
    {
        if (!$this->isCsrfTokenValid('change-role', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_users_index');
        }

        $user->setRoles([$request->request->get('roles')]);

        $this->manager->flush();

        $this->addFlash('success', 'User account roles changed successfully');

        return $this->redirectToRoute('admin_users_index');
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"})
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function deletePermanentlyAccount (Request $request, User $user)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_users_index');
        }

        $this->manager->remove($user);
        $this->manager->flush();

        $this->addFlash('success', 'User account deleted definitely successfully');

        return $this->redirectToRoute('admin_users_index');
    }

}
