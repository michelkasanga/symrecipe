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
        5);
    return $this->render('pages/recipe/index.html.twig', compact('recipes'));
}

#[Route('/recette/new', name:'app_recipe_new', methods:['GET',' POST'])]
public function new(): Response
{
    $recipe = new Recipe();
    $form = $this->createForm(RecipeType::class, $recipe);
    return  $this->render('pages/recipe/new.html.twig',['form' => $form->createView()]);
}
}
