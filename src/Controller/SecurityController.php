<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class SecurityController extends AbstractController
{
    #[Route('/logIn', name: 'app_security', methods:['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('pages/security/index.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('/logOut', name: 'app_security_logout', methods:['GET', 'POST'])]
    public function logout()
    {
      
    }

    
    #[Route('/signUp', name: 'app_security_sign_up', methods:['GET', 'POST'])]
    public function registration( Request $request, UserPasswordHasherInterface $hasher  , EntityManagerInterface $manager): Response
    {
        $user = new User();
        $form  = $this->createForm(RegistrationType::class, $user);
 
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
          
           $user->setPassword(
            $hasher->hashPassword($user, $user->getPlainPassword())
           );
          $manager->persist($form->getData());
          $manager->flush();
     
            $this->addFlash(
                'success',
                'votre compte a ete cree avec success!'
            );
             
            return $this->redirectToRoute('app_security');
        }


        return $this->render('pages/security/signUp.html.twig', [
            'form' =>  $form->createView()
        ]);
    }
}
