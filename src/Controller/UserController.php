<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/update/{id}', name: 'app_user_upadate', methods:['GET','POST'])]
    public function index(User $user, Request $request, UserRepository $repos): Response
    {
        if(!$this->getUser())
        {
            return  $this->redirectToRoute('app_security');
        }
        if($this->getUser() !== $user)
        {
            return $this->redirectToRoute('app_recipe');
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $repos->save($form->getData(), true);
            return  $this->redirectToRoute('app_home');
        }

        return $this->render('pages/user/update.html.twig', [
          'form' => $form->createView()
        ]);
    }
}
