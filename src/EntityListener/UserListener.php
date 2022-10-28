<?php 

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class  UserListener 
{
    private UserPasswordHasherInterface $hasher;

    public function  __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function prePersist(User $user)
    {
        $this->encodePassWord($user);
    }

    public function preUpdate(User $user)
    {
        $this->encodePassWord($user);
    }

    public function encodePassWord(User $user)
    {
        if($user->getPlainPassword() === null)
        {
            return;
        }
        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $user->getPlainPassword()
            )
        );
       
    }
}