<?php

namespace App\Http\Controller;

use App\Domain\Category\Category;
use App\Domain\Recipe\Recipe;
use App\Helper\Paginator\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, private readonly PaginatorInterface $paginator)
    {
        parent::__construct($em);
    }

    #[Route(path: '/', name: 'home')]
    public function index(Request $request): Response
    {
        $repo = $this->em->getRepository(Recipe::class);

        // Filtre favoris
        $favoritesOnly = $request->query->getBoolean('favorite');
        $query = $repo->findAllQuery();

        if ($favoritesOnly) {
            $user = $this->getUserOrThrow();
            $query = $repo->findFavoritesQuery($user);
        }

        // Filtre catégorie
        $categories = $this->em->getRepository(Category::class)->findAll();
        $category = $request->query->get('category');
        if ($category) {
            if ($category === 'none') {
                $query
                    ->leftJoin('r.categories', 'category')
                    ->where('category IS NULL');
            } else {
                $query
                    ->join('r.categories', 'category')
                    ->where('category = :category')
                    ->setParameter('category', $category);
            }
        }

        $page = $request->query->getInt('page', 1);
        $recipes = $this->paginator->paginate($query->setMaxResults(10)->getQuery());

        return $this->render('pages/home.html.twig', [
            'recipes' => $recipes,
            'page' => $page,
            'menuItem' => 'home',
            'favorites_only' => $favoritesOnly,
            'categories' => $categories,
        ]);
    }
}
