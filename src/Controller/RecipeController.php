<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name:'app_recipe', methods:['GET'])]
public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
    $recipes = $paginator->paginate(
        $repository->findBy(['user' =>$this->getUser()]),
        $request->query->getInt('page', 1),
        5
    );
    return $this->render('pages/recipe/index.html.twig', compact('recipes'));
}


/*create a new recipe
 *
 * @param Request $request
 * @param RecipeRepository $repository
 * @return Response
 */
#[IsGranted('ROLE_USER')]
#[Route('/recipe/new', name:'app_recipe_new', methods:['GET', 'POST'])]
public function new (Request $request, EntityManagerInterface $manager): Response {
    $recipe = new Recipe();
    $form = $this->createForm(RecipeType::class, $recipe);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $recipe = $form->getData();
        $recipe->setUser($this->getUser());
        $this->addFlash(
            'success',
            'votre recette a ete ajouter !'
        );
        $manager->persist($recipe);
        $manager->flush();
        return $this->redirectToRoute('app_recipe');
    }
    return $this->render('pages/recipe/new.html.twig', ['form' => $form->createView()]);
}


#[Route('/recipe/public', name:'app_recipe_public', methods:['GET'])]
public function indexPublic(PaginatorInterface $paginator, RecipeRepository $repository, Request $request): Response
{
    $recipes = $paginator->paginate(
        $repository->findPublicRecipe(null),
        $request->query->getInt('page', 1),
        5
    );
    return $this->render('pages/recipe/index_public.html.twig', ['recipes' => $recipes]);
}


#[Security("is_granted('ROLE_USER') and recipe.isIsPublic() === true")]
#[Route('/recipe/{id}', name:'app_show', methods:['GET'])]
public function show(Recipe $recipe)
{
    return $this->render('pages/recipe/show.html.twig', ['recipe' => $recipe]);
} 


#[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
#[Route('/recipe/edit/{id}', name:'app_recipe_edit', methods:['GET', 'POST'])]
public function edit( Request $request, EntityManagerInterface $manager , Recipe $recipe): Response
{
    $form = $this->createForm(RecipeType::class, $recipe);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
       $recipe = $form->getData();
       $recipe->setUpdateAt( new DateTimeImmutable());
        $this->addFlash(
            'success',
            'votre recette a ete modifier avec success!'
        );
        $manager->persist($recipe);
        $manager->flush();
        return $this->redirectToRoute('app_recipe');
    }
    return $this->render('pages/recipe/edit.html.twig',  ['form' => $form->createView()]);
}

#[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
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
