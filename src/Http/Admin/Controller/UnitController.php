<?php

namespace App\Http\Admin\Controller;

use App\Domain\Unit\Unit;
use App\Http\Admin\Form\UnitForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/units', name: 'unit_')]
class UnitController extends CrudController
{
    protected string $entity = Unit::class;

    protected ?string $templatePath = 'unit';

    protected ?string $routePrefix = 'admin_unit';

    protected ?string $menuItem = 'unit';

    protected ?string $searchField = 'label';

    protected string $formClass = UnitForm::class;

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
        return $this->crudNew(new Unit());
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Unit $unit): Response
    {
        return $this->crudEdit($unit);
    }

    #[Route(path: '/delete/{id<\d+>}', name: 'delete', methods: ['GET'])]
    public function delete(Unit $unit): RedirectResponse
    {
        return $this->crudDelete($unit);
    }
}
