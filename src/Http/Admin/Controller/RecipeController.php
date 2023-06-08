<?php

namespace App\Http\Admin\Controller;

use App\Domain\Recipe\Recipe;
use App\Http\Admin\Form\RecipeForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'recipe_')]
class RecipeController extends AbstractController
{
    protected string $entity = Recipe::class;
    protected string $templatePath = 'recipe';
    protected string $routePrefix = 'admin_recipe';

    /** @var array{'update': ?string, 'delete': ?string, 'create': ?string} */
    private array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    private string $formClass = RecipeForm::class;

    #[Route(path: '/', name: 'index')]
    public function index(Request $request): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderBy('row.createdAt', 'DESC');

        if ($request->get('q')) {
            $query = $this->applySearch(trim((string)$request->get('q')), $query);
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
        $entity = (new Recipe())->setUser($this->getUser());

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
                'description' => 'Le contenu a bien été créé'
            ]);

            return $this->redirectAfterSave($entity);
        }

        return $this->render("admin/{$this->templatePath}/new.html.twig", [
            'form' => $form->createView(),
            'entity' => $entity
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, Recipe $recipe): Response
    {
        $form = $this->createForm($this->formClass, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $old = clone $recipe;
            $this->em->flush();
            if ($this->events['update'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['update']($recipe, $old));
            }
            $this->addFlash('success', [
                'title' => 'Succès',
                'description' => 'Le contenu a bien été modifié'
            ]);

            return $this->redirectAfterSave($recipe);
        }

        return $this->render("admin/{$this->templatePath}/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $recipe
        ]);
    }

    #[Route(path: '/delete/{id<\d+>}', name: 'delete', methods: ['GET'])]
    public function delete(Recipe $recipe): RedirectResponse
    {
        $this->em->remove($recipe);
        if ($this->events['delete'] ?? null) {
            $this->dispatcher->dispatch(new $this->events['delete']($recipe));
        }
        $this->em->flush();
        $this->addFlash('success', [
            'title' => 'Succès',
            'description' => 'Le contenu a bien été supprimé'
        ]);

        return $this->redirectToRoute($this->routePrefix . '_index');
    }
}
