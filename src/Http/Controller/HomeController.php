<?php

namespace App\Http\Controller;

use App\Domain\Recipe\Recipe;
use App\Helper\Paginator\Paginator;
use App\Helper\Paginator\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, private readonly PaginatorInterface $paginator)
    {
        parent::__construct($em);

    #[Route(path: '/', name: 'home')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $query = $this->em->getRepository(Recipe::class)->findAllQuery();

        $recipes = $this->paginator->paginate($query->setMaxResults(10)->getQuery());

        return $this->render('pages/home.html.twig', [
            'recipes' => $recipes,
            'page' => $page
        ]);
    }
}
