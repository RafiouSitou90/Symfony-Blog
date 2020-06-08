<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesFormType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminCategoriesController
 * @package App\Controller\Admin
 *
 * @Route("/admin/categories", name="admin_categories_")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminCategoriesController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var CategoriesRepository
     */
    private CategoriesRepository $categoriesRepository;

    /**
     * AdminCategoriesController constructor.
     * @param EntityManagerInterface $manager
     * @param CategoriesRepository $categoriesRepository
     */
    public function __construct (EntityManagerInterface $manager, CategoriesRepository $categoriesRepository)
    {
        $this->manager = $manager;
        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * @Route("", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $this->categoriesRepository->findBy([], ['name' => 'ASC'])
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new (Request $request)
    {
        $category = new Categories();

        $form = $this->createForm(CategoriesFormType::class, $category)
            ->add('saveAndCreateNew', SubmitType::class, [
                'label' => 'Save and create new'
            ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($category);
            $this->manager->flush();

            $this->addFlash('success', 'New article created successfully');

            /** @var ClickableInterface $saveAndCreateNewButton */
            $saveAndCreateNewButton = $form->get('saveAndCreateNew');

            if ($saveAndCreateNewButton->isClicked()) {
                return $this->redirectToRoute('admin_categories_new');
            }

            return $this->redirectToRoute('admin_articles_index');
        }

        return $this->render('admin/categories/new/index.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     *
     * @param Categories $category
     * @return Response
     */
    public function show (Categories $category)
    {
        return $this->render('admin/categories/show/index.html.twig', ['category' => $category]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Categories $category
     * @return RedirectResponse|Response
     */
    public function edit (Request $request, Categories $category)
    {
        $form = $this->createForm(CategoriesFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success', 'Category updated successfully');

            return $this->redirectToRoute('admin_categories_edit', ['id' => $category->getId()]);
        }

        return $this->render('admin/categories/edit/index.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"})
     *
     * @param Request $request
     * @param Categories $category
     * @return Response
     */
    public function delete(Request $request, Categories $category): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_categories_index');
        }

        $this->manager->remove($category);
        $this->manager->flush();

        $this->addFlash('success', 'Category deleted successfully');

        return $this->redirectToRoute('admin_categories_index');
    }
}
