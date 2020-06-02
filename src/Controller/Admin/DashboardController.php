<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 *
 * @Route("/admin", name="app_admin_")
 * @IsGranted("ROLE_ADMIN")
 * @package App\Controller\Admin
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("", name="dashboard", methods={"GET"})
     *
     * @return Response
     */
    public function index ()
    {
        return $this->render('admin/dashboard/index.html.twig');
    }
}
