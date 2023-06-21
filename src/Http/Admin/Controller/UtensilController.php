<?php

namespace App\Http\Admin\Controller;

use App\Domain\Utensil\Utensil;
use App\Http\Admin\Form\UtensilForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/utensils', name: 'utensil_')]
class UtensilController extends CrudController
{
    protected string $entity = Utensil::class;

    protected ?string $templatePath = 'utensil';

    protected ?string $routePrefix = 'admin_utensil';

    protected ?string $menuItem = 'utensil';

    protected ?string $searchField = 'name';

    protected string $formClass = UtensilForm::class;

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
    public function new(): Response
    {
        return $this->crudNew(new Utensil());
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Utensil $utensil): Response
    {
        return $this->crudEdit($utensil);
    }

    #[Route(path: '/delete/{id<\d+>}', name: 'delete', methods: ['GET'])]
    public function delete(Utensil $utensil): RedirectResponse
    {
        return $this->crudDelete($utensil);
    }
}
