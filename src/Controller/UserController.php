<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/update/{id}', name:'app_user_upadate', methods:['GET', 'POST'])]
public function index(User $user, Request $request, UserRepository $repos, UserPasswordHasherInterface $hasher): Response
    {
    if (!$this->getUser()) {
        return $this->redirectToRoute('app_security');
    }
    if ($this->getUser() !== $user) {
        return $this->redirectToRoute('app_recipe');
    }
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {
            $repos->save($form->getData(), true);
            $this->addFlash(
                'success',
                'vos information ont etait modifier avec succes'
            );
            return $this->redirectToRoute('app_recipe');

        } else {
            $this->addFlash(
                'danger',
                'votre mot de passe est  incorrect'
            );
        }
    }

    return $this->render('pages/user/update.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/user/updatePassword/{id}', name:'app_user_upadate_password', methods:['GET', 'POST'])]
public function updatePasswordUser(User $user, Request $request,EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_security');
        }
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_recipe');
        }
    $form = $this->createForm(UserPasswordType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) { 
                    $user->setUpdatedAt(new \DateTimeImmutable());
                    // $user->setPlainPassword(
                    //     $form->getData()['newPassword']
                    // );
                    $user->setPassword( 
                        $hasher->hashPassword($user, $form->getData()['newPassword'])
                    );
                        
            $this->addFlash(
                'success',
                'votre mot de passe a ete modifier avec success'
            );
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_recipe');
        }else{
            $this->addFlash(
                'danger',
                'votre mot de passe est  incorrect'
            );
        }
    }
    return $this->render('pages/user/updatePassword.html.twig', ['form' => $form->createView()]);
}

}
