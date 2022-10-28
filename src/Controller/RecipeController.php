<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    #[Route('/recette', name:'app_recipe', methods:['GET'])]
public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
    $recipes = $paginator->paginate(
        $repository->findAll(),
        $request->query->getInt('page', 1),
        5
    );
    return $this->render('pages/recipe/index.html.twig', compact('recipes'));
}

#[Route('/recette/new', name:'app_recipe_new', methods:['GET', 'POST'])]
/*create a new recipe
 *
 * @param Request $request
 * @param RecipeRepository $repository
 * @return Response
 */
#[Route('/recette/new', name:'app_recipe_new', methods:['GET', 'POST'])]
public function new (Request $request, RecipeRepository $repository): Response {
    $recipe = new Recipe();
    $form = $this->createForm(RecipeType::class, $recipe);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $repository->save($form->getData(), true);
        $this->addFlash(
            'success',
            'votre recette a ete ajouter !'
        );
        return $this->redirectToRoute('app_recipe');
    }
    return $this->render('pages/recipe/new.html.twig', ['form' => $form->createView()]);
}

#[Route('/recipe/edit/{id}', name:'app_recipe_edit', methods:['GET', 'POST'])]
public function edit( Request $request, RecipeRepository $repository, Recipe $ingredient): Response
{
    $form = $this->createForm(RecipeType::class, $ingredient);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $repository->save($form->getData(), true);
        $this->addFlash(
            'success',
            'votre recette a ete modifier avec success!'
        );
        return $this->redirectToRoute('app_recipe');
    }
    return $this->render('pages/recipe/edit.html.twig',  ['form' => $form->createView()]);
}


#[Route('/recipe/delete/{id}', name:'app_recipe_delete', methods:[ 'GET'])]
public function delete(Recipe $recipe, RecipeRepository $repository): Response
{
    $repository->remove($recipe, true);
        $this->addFlash(
            'success',
            'votre recette a ete supprimer avec success!'
        );
        return $this->redirectToRoute('app_recipe');
}

}
