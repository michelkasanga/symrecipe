<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Security("is_granted('ROLE_USER') and user === User")]
#[Route('/user/update/{id}', name:'app_user_update', methods:['GET', 'POST'])]
function index(User $User, Request $request, UserRepository $repos, UserPasswordHasherInterface $hasher): Response
    {

    $form = $this->createForm(UserType::class, $User);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        if ($hasher->isPasswordValid($User, $form->getData()->getPlainPassword())) {
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

#[Security("is_granted('ROLE_USER') and user === User")]
#[Route('/user/updatePassword/{id}', name:'app_user_update_password', methods:['GET', 'POST'])]
function updatePasswordUser(User $User, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {

    $form = $this->createForm(UserPasswordType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        if ($hasher->isPasswordValid($User, $form->getData()['plainPassword'])) {
            $User->setUpdatedAt(new \DateTimeImmutable());
            // $user->setPlainPassword(
            //     $form->getData()['newPassword']
            // );
            $User->setPassword(
                $hasher->hashPassword($User, $form->getData()['newPassword'])
            );

            $this->addFlash(
                'success',
                'votre mot de passe a ete modifier avec success'
            );
            $manager->persist($User);
            $manager->flush();

            return $this->redirectToRoute('app_recipe');
        } else {
            $this->addFlash(
                'danger',
                'votre mot de passe est  incorrect'
            );
        }
    }
    return $this->render('pages/user/updatePassword.html.twig', ['form' => $form->createView()]);
}

}
