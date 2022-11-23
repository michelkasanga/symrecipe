<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/ ', name:'app_home')]
function index(PaginatorInterface $paginator, RecipeRepository $repository, Request $request): Response
    {
    $recipes = $paginator->paginate(
        $repository->findPublicRecipe(null),
        $request->query->getInt('page', 1),
        6
    );
    return $this->render('pages/index.html.twig', ['recipes' => $recipes]);
}
}
