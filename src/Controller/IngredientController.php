<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{

   
    /**
     * controller for all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name:'app_ingredient')]
    #[IsGranted('ROLE_USER')]
public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
    $ingredient = $paginator->paginate(
        $repository->findBy(['user' =>$this->getUser()]),
        $request->query->getInt('page', 1),
        10
    );
    return $this->render('pages/ingredient/index.html.twig', compact('ingredient'));
}

#[IsGranted('ROLE_USER')]
#[Route('/ingredient/new', name:'app_new_ingredient', methods:['GET', 'POST'])]
public function new (Request $request, EntityManagerInterface $manager): Response {
    $ingredient = new Ingredient();
    $form = $this->createForm(IngredientType::class, $ingredient);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $ingredient = $form->getData();
        $ingredient->setUser($this->getUser());
        $this->addFlash(
            'success',
            'votre ingredient a ete ajouter !'
        );

        $manager->persist($ingredient);
        $manager->flush();
        return $this->redirectToRoute('app_ingredient');
    }
    return $this->render('pages/ingredient/new.html.twig', ['form' => $form->createView()]);
}


/**
 * Undocumented function
 *
 * @param Request $request
 * @param IngredientRepository $repository
 * @param Ingredient $ingredient
 * @return Response
 */
#[Security("is_granted('ROLE_USER') and user ===ingredient.getUser()")]
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

#[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
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
