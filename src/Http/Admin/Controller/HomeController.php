<?php

namespace App\Http\Admin\Controller;

use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'home')]
    public function home(): Response
    {
        return $this->render('admin/home.html.twig');
    }
}
