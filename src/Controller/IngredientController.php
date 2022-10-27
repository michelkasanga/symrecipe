<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{

    #[Route('/ingredient', name:'app_ingredient')]
public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
    $ingredient = $paginator->paginate(
        $repository->findAll(),
        $request->query->getInt('page', 1),
        10
    );
    return $this->render('pages/ingredient/index.html.twig', compact('ingredient'));
}

#[Route('/ingredient/new', name:'app_new_ingredient', methods:['GET', 'POST'])]
public function new (Request $request, IngredientRepository $repository): Response {
    $ingredient = new Ingredient();
    $form = $this->createForm(IngredientType::class, $ingredient);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $repository->save($form->getData(), true);
        $this->addFlash(
            'success',
            'votre ingredient a ete ajouter !'
        );
        return $this->redirectToRoute('app_ingredient');
    }
    return $this->render('pages/ingredient/new.html.twig', ['form' => $form->createView()]);
}

#[Route('/ingredient/edit/{id}', name:'app_ingredient_edit', methods:['GET', 'POST'])]
public function edit( Request $request, IngredientRepository $repository, Ingredient $ingredient): Response
{
    $form = $this->createForm(IngredientType::class, $ingredient);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $repository->save($form->getData(), true);
        $this->addFlash(
            'success',
            'votre ingredient a ete modifier avec success!'
        );
        return $this->redirectToRoute('app_ingredient');
    }
    return $this->render('pages/ingredient/edit.html.twig',  ['form' => $form->createView()]);
}


#[Route('/ingredient/delete/{id}', name:'app_ingredient_delete', methods:[ 'GET'])]
public function delete(Ingredient $ingredient, IngredientRepository $repository): Response
{
    $repository->remove($ingredient, true);
        $this->addFlash(
            'success',
            'votre ingredient a ete supprimer avec success!'
        );
        return $this->redirectToRoute('app_ingredient');
}

#[Route('/model', name:'app_model')]
public function model(): Response
    {

    return $this->render('pages/model.html.twig');
}

}
