<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use App\Repository\TagsRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticlesController
 * @package App\Controller
 *
 * @Route("/articles", name="app_articles_")
 */
class ArticlesController extends AbstractController
{
    /**
     * @var ArticlesRepository
     */
    private ArticlesRepository $articlesRepository;
    /**
     * @var TagsRepository
     */
    private TagsRepository $tagsRepository;

    /**
     * ArticlesController constructor.
     * @param ArticlesRepository $articlesRepository
     * @param TagsRepository $tagsRepository
     */
    public function __construct (
        ArticlesRepository $articlesRepository,
        TagsRepository $tagsRepository)
    {
        $this->articlesRepository = $articlesRepository;
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @Route("", defaults={"page": "1"}, name="index", methods={"GET"})
     * @Route("/page/{page<[1-9]\d*>}", defaults={"page": "1"}, name="index_paginated", methods={"GET"})
     *
     * @param Request $request
     * @param int $page
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, int $page)
    {
        $tag = null;
        if ($request->query->has('tag')) {
            $tag = $this->tagsRepository->findOneBy(['name' => $request->query->get('tag')]);
        }
        $allLatestArticles = $this->articlesRepository->findAllLatest($page, $tag);

        return $this->render('articles/index.html.twig', [
            'articles_paginator' => $allLatestArticles
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
     *
     * @param string $slug
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show (string $slug)
    {
        $article = $this->articlesRepository->findOneBySlug($slug);

        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }

        return $this->render('articles/show/index.html.twig', ['article' => $article]);
    }
}
