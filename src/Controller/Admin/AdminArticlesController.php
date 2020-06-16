<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Entity\User;
use App\Form\ArticlesFormType;
use App\Repository\ArticlesRepository;
use App\Security\Voter\ArticleVoter;
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
 * Class AdminArticlesController
 * @package App\Controller\Admin
 *
 * @Route("/admin/articles", name="admin_articles_")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminArticlesController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var ArticlesRepository
     */
    private ArticlesRepository $articlesRepository;

    /**
     * AdminArticlesController constructor.
     * @param EntityManagerInterface $manager
     * @param ArticlesRepository $articlesRepository
     */
    public function __construct (
        EntityManagerInterface $manager,
        ArticlesRepository $articlesRepository)
    {
        $this->manager = $manager;
        $this->articlesRepository = $articlesRepository;
    }

    /**
     * @Route("", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index()
    {
        $authorArticles = $this->articlesRepository->findBy(['author' => $this->getUser()], ['publishedAt' => 'DESC']);

        return $this->render('admin/articles/index.html.twig', ['articles' => $authorArticles]);
    }

    /**
     * @Route("/new", name="new", methods={"POST", "GET"})
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new (Request $request)
    {
        $article = new Articles();

        /** @var User $user */
        $user = $this->getUser();
        $article->setAuthor($user);

        $form = $this->createForm(ArticlesFormType::class, $article)
            ->add('saveAndCreateNew', SubmitType::class, [
                'label' => 'Save and create new'
            ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($article->getArticleStatus() !== Articles::PUBLISHED()) {
                $article->setPublishedAt(null);
            }

            $this->manager->persist($article);
            $this->manager->flush();

            $this->addFlash('success', 'New article created successfully');

            /** @var ClickableInterface $saveAndCreateNewButton */
            $saveAndCreateNewButton = $form->get('saveAndCreateNew');

            if ($saveAndCreateNewButton->isClicked()) {
                return $this->redirectToRoute('admin_articles_new');
            }

            return $this->redirectToRoute('admin_articles_index');
        }

        return $this->render('admin/articles/new/index.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     *
     * @param Articles $article
     * @return Response
     */
    public function show (Articles $article)
    {
        $this->denyAccessUnlessGranted(
                ArticleVoter::SHOW, $article, 'Access denied! Sorry you cannot show this article');

        return $this->render('admin/articles/show/index.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Articles $article
     * @return RedirectResponse|Response
     *
     * @IsGranted("edit", subject="article", message="Access denied! Sorry you cannot edit this article")
     */
    public function edit (Request $request, Articles $article)
    {
        $form = $this->createForm(ArticlesFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success', 'Article updated successfully');

            return $this->redirectToRoute('admin_articles_edit', ['id' => $article->getId()]);
        }

        return $this->render('admin/articles/edit/index.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"})
     *
     * @param Request $request
     * @param Articles $article
     * @return Response
     *
     * @IsGranted("delete", subject="article", message="Access denied! Sorry you cannot delete this article")
     */
    public function delete(Request $request, Articles $article): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_articles_index');
        }
        $article->getTags()->clear();

        $this->manager->remove($article);
        $this->manager->flush();

        $this->addFlash('success', 'Article deleted successfully');

        return $this->redirectToRoute('admin_articles_index');
    }
}
