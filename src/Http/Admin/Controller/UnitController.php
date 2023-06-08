<?php

namespace App\Http\Admin\Controller;

use App\Domain\Unit\Unit;
use App\Http\Admin\Form\UnitForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/units', name: 'unit_')]
class UnitController extends AbstractController
{
    protected string $entity = Unit::class;
    protected string $templatePath = 'unit';
    protected string $routePrefix = 'admin_unit';

    /** @var array{'update': ?string, 'delete': ?string, 'create': ?string} */
    private array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    private string $formClass = UnitForm::class;

    protected string $searchField = 'label';

    #[Route(path: '/', name: 'index')]
    public function index(Request $request): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row');

        if ($request->get('q')) {
            $query = $this->applySearch(trim((string) $request->get('q')), $query);
        }

        $rows = $this->paginator->paginate($query->getQuery());

        return $this->render("admin/{$this->templatePath}/index.html.twig", [
            'rows' => $rows,
            'searchable' => true,
            'prefix' => $this->routePrefix,
        ]);
    }

    #[Route(path: '/new', name: 'new', methods: ['POST', 'GET'])]
    public function new(Request $request): Response
    {
        $entity = new Unit();

        $form = $this->createForm($this->formClass, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($entity);
            $this->em->flush();
            if ($this->events['create'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['create']($entity));
            }
            $this->addFlash('success', [
                'title' => 'Succès',
                'description' => 'Le contenu a bien été créé',
            ]);

            return $this->redirectAfterSave($entity);
        }

        return $this->render("admin/{$this->templatePath}/new.html.twig", [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, Unit $unit): Response
    {
        $form = $this->createForm($this->formClass, $unit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old = clone $unit;
            $this->em->flush();
            if ($this->events['update'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['update']($unit, $old));
            }
            $this->addFlash('success', [
                'title' => 'Succès',
                'description' => 'Le contenu a bien été modifié',
            ]);

            return $this->redirectAfterSave($unit);
        }

        return $this->render("admin/{$this->templatePath}/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $unit,
        ]);
    }

    #[Route(path: '/delete/{id<\d+>}', name: 'delete', methods: ['GET'])]
    public function delete(Unit $unit): RedirectResponse
    {
        $this->em->remove($unit);
        if ($this->events['delete'] ?? null) {
            $this->dispatcher->dispatch(new $this->events['delete']($unit));
        }
        $this->em->flush();
        $this->addFlash('success', [
            'title' => 'Succès',
            'description' => 'Le contenu a bien été supprimé',
        ]);

        return $this->redirectToRoute($this->routePrefix.'_index');
    }
}
