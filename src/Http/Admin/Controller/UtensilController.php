<?php

namespace App\Http\Admin\Controller;

use App\Domain\Utensil\Utensil;
use App\Http\Admin\Form\UtensilForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/utensils', name: 'utensil_')]
class UtensilController extends AbstractController
{
    protected string $entity = Utensil::class;
    protected string $templatePath = 'utensil';
    protected string $routePrefix = 'admin_utensil';

    /** @var array{'update': ?string, 'delete': ?string, 'create': ?string} */
    private array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    private string $formClass = UtensilForm::class;

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
        $entity = new Utensil();

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
    public function edit(Request $request, Utensil $utensil): Response
    {
        $form = $this->createForm($this->formClass, $utensil);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old = clone $utensil;
            $this->em->flush();
            if ($this->events['update'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['update']($utensil, $old));
            }
            $this->addFlash('success', [
                'title' => 'Succès',
                'description' => 'Le contenu a bien été modifié',
            ]);

            return $this->redirectAfterSave($utensil);
        }

        return $this->render("admin/{$this->templatePath}/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $utensil,
        ]);
    }

    #[Route(path: '/delete/{id<\d+>}', name: 'delete', methods: ['GET'])]
    public function delete(Utensil $utensil): RedirectResponse
    {
        $this->em->remove($utensil);
        if ($this->events['delete'] ?? null) {
            $this->dispatcher->dispatch(new $this->events['delete']($utensil));
        }
        $this->em->flush();
        $this->addFlash('success', [
            'title' => 'Succès',
            'description' => 'Le contenu a bien été supprimé',
        ]);

        return $this->redirectToRoute($this->routePrefix.'_index');
    }
}
