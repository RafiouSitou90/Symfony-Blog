<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comments;
use App\Entity\CommentsResponses;
use App\Entity\User;
use App\Event\CommentsResponsesCreatedEvent;
use App\Form\CommentsResponsesFormType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentsResponsesController
 * @package App\Controller
 *
 * @Route("/comments/responses", name="app_comments_responses_")
 */
class CommentsResponsesController extends AbstractController
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
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * CommentsController constructor.
     * @param EntityManagerInterface $manager
     * @param EventDispatcherInterface $eventDispatcher
     * @param ArticlesRepository $articlesRepository
     */
    public function __construct (
        EntityManagerInterface $manager,
        EventDispatcherInterface $eventDispatcher,
        ArticlesRepository $articlesRepository)
    {
        $this->manager = $manager;
        $this->articlesRepository = $articlesRepository;
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * @Route("/{commentId}/new", name="new", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @ParamConverter("comment", options={"mapping": {"commentId": "id"}})
     * @param Request $request
     * @param Comments $comment
     *
     * @return RedirectResponse|Response
     */
    public function create (Request $request, Comments $comment)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Articles $article */
        $article = $comment->getArticle();

        $commentResponse = (new CommentsResponses())->setAuthor($user);
        $comment->addCommentResponse($commentResponse);

        $form = $this->createForm(CommentsResponsesFormType::class, $commentResponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($commentResponse);
            $this->manager->flush();

            $this->eventDispatcher->dispatch(new CommentsResponsesCreatedEvent($commentResponse));

            return $this->redirectToRoute('app_articles_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('comments_responses/_form_error.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param CommentsResponses $commentsResponses
     *
     * @return RedirectResponse|Response
     */
    public function edit (Request $request, CommentsResponses $commentsResponses)
    {
        /** @var Comments $comment */
        $comment = $commentsResponses->getComment();

        /** @var Articles $article */
        $article = $comment->getArticle();

        $form = $this->createForm(CommentsResponsesFormType::class, $commentsResponses);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            return $this->redirectToRoute('app_articles_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('comments_responses/edit/_edit_form.html.twig', [
            'commentResponse' => $commentsResponses,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param CommentsResponses $commentsResponses
     *
     * @return RedirectResponse|Response
     */
    public function delete (Request $request, CommentsResponses $commentsResponses)
    {
        /** @var Comments $comment */
        $comment = $commentsResponses->getComment();

        /** @var Articles $articleTmp */
        $articleTmp = $comment->getArticle();

        /** @var Articles $article */
        $article = $this->articlesRepository->findOneBy(['id' => $articleTmp->getId()]);

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('app_articles_show', ['slug' => $article->getSlug()]);
        }

        $this->manager->remove($commentsResponses);
        $this->manager->flush();

        return $this->redirectToRoute('app_articles_show', ['slug' => $article->getSlug()]);
    }

    /**
     * @param Comments $comment
     * @return Response
     */
    public function createCommentForm(Comments $comment)
    {
        $form = $this->createForm(CommentsResponsesFormType::class);

        return $this->render('comments_responses/_form.html.twig', [
            'commentResponse' => $comment,
            'form' => $form->createView(),
        ]);
    }
}
