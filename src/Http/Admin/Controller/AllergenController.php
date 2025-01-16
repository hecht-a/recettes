<?php

namespace App\Http\Admin\Controller;

use App\Domain\Allergen\Allergen;
use App\Http\Admin\Form\AllergenForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @extends CrudController<Allergen>
 */
#[Route(path: '/allergens', name: 'allergen_')]
class AllergenController extends CrudController
{
    protected string $entity = Allergen::class;

    protected ?string $templatePath = 'allergen';

    protected ?string $routePrefix = 'admin_allergen';

    protected ?string $menuItem = 'allergen';

    protected ?string $searchField = 'name';

    protected string $formClass = AllergenForm::class;

    /** @var array{'update': ?string, 'delete': ?string, 'create': ?string} */
    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/new', name: 'new', methods: ['POST', 'GET'])]
    public function new(Request $request): Response
    {
        return $this->crudNew(new Allergen());
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Allergen $allergen): Response
    {
        return $this->crudEdit($allergen);
    }

    #[Route(path: '/delete/{id<\d+>}', name: 'delete', methods: ['GET'])]
    public function delete(Allergen $allergen): RedirectResponse
    {
        return $this->crudDelete($allergen);
    }
}
