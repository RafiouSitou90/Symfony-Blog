<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use App\Repository\TagsRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticlesController
 * @package App\Controller
 *
 * @Route("/blog", name="app_articles_")
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
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * ArticlesController constructor.
     * @param ArticlesRepository $articlesRepository
     * @param PaginatorInterface $paginator
     * @param TagsRepository $tagsRepository
     */
    public function __construct (
        ArticlesRepository $articlesRepository,
        PaginatorInterface $paginator,
        TagsRepository $tagsRepository)
    {
        $this->articlesRepository = $articlesRepository;
        $this->tagsRepository = $tagsRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("", name="index", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(Request $request)
    {
        $tag = null;
        if ($request->query->has('tag')) {
            $tag = $this->tagsRepository->findOneBy(['name' => $request->query->get('tag')]);
        }
//        $allLatestArticles = $this->articlesRepository->findAllLatest($page, $tag);
        $allLatestArticles = $this->paginator->paginate(
            $this->articlesRepository->findAllLatest($tag),
            $request->query->getInt('page', 1),10
        );

//        dd($allLatestArticles);

        return $this->render('articles/index.html.twig', [
            'articles' => $allLatestArticles
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
