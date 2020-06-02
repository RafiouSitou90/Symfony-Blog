<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 *
 * @Route("/", name="app_")
 * @IsGranted("ROLE_USER")
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @Route("", name="home", methods={"GET"})
     *
     * @return Response
     */
    public function index ()
    {
        return $this->render('home/index.html.twig');
    }
}
