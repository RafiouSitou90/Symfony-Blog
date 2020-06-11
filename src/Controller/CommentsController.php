<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comments;
use App\Entity\User;
use App\Event\CommentsCreatedEvent;
use App\Form\CommentsFormType;
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
 * Class CommentsController
 * @package App\Controller
 *
 * @Route("/comments", name="app_comments_")
 */
class CommentsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * CommentsController constructor.
     * @param EntityManagerInterface $manager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct (EntityManagerInterface $manager, EventDispatcherInterface $eventDispatcher)
    {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/{articleSlug}/new", name="new", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @ParamConverter("article", options={"mapping": {"articleSlug": "slug"}})
     * @param Request $request
     * @param Articles $article
     *
     * @return RedirectResponse|Response
     */
    public function create (Request $request, Articles $article)
    {
        /** @var User $user */
        $user = $this->getUser();

        $comment = (new Comments())->setAuthor($user);
        $article->addComment($comment);

        $form = $this->createForm(CommentsFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($comment);
            $this->manager->flush();

            $this->eventDispatcher->dispatch(new CommentsCreatedEvent($comment));

            return $this->redirectToRoute('app_articles_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('comments/_form_error.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param Comments $comment
     *
     * @return RedirectResponse|Response
     */
    public function edit (Request $request, Comments $comment)
    {
        /** @var Articles $article */
        $article = $comment->getArticle();
        $form = $this->createForm(CommentsFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            return $this->redirectToRoute('app_articles_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('comments/edit/_edit_form.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param Comments $comment
     *
     * @return RedirectResponse|Response
     */
    public function delete (Request $request, Comments $comment)
    {
        /** @var Articles $article */
        $article = $comment->getArticle();

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('app_articles_show', ['slug' => $article->getSlug()]);
        }

        $this->manager->remove($comment);
        $this->manager->flush();

        return $this->redirectToRoute('app_articles_show', ['slug' => $article->getSlug()]);
    }

    /**
     * @param Articles $article
     * @return Response
     */
    public function createCommentForm(Articles $article)
    {
        $form = $this->createForm(CommentsFormType::class);

        return $this->render('comments/_form.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }
}
