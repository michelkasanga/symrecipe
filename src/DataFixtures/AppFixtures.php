<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\Mark;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
   private  Generator $faker;
   private $hasher;

    public function __construct( UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->hasher = $hasher;

    }
    public function load(ObjectManager $manager ): void
    {

        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->hasher->hashPassword($user, 'password'));
            $users[] = $user;
            $manager->persist($user);
        }

        for ($i = 0; $i < 50; $i++) {
            //ingrendient
            $ingredients = [];
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(1, 100))
                ->setUser($users[mt_rand(0, count($users) -1)]);
                
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        for ($j = 0; $j < 25; $j++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
               ->setUser($users[mt_rand(0, count($users) -1)]) ;

            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) - 1)]);
            }
            $recipes[] = $recipe;
            $manager->persist($recipe);
        }

        foreach ($recipes as $recipe) {
            for ($i=0; $i < mt_rand(0, 4); $i++) { 
                $mark = new Mark();
                $mark->setMark(mt_rand(1, 5))
                ->setUser($users[mt_rand(0, count($users) - 1)])
                ->setRecipe($recipe);
                $manager->persist($mark);
            }
        }

        for ($i=0; $i < 5 ; $i++) { 
            $contact = new Contact();
            $contact->setFullName($this->faker->name())
                            ->setEmail($this->faker->email())
                            ->setSubject('Demande numero '.($i+1))
                            ->setMessage($this->faker->text());
                        $manager->persist($contact);
        }
        $manager->flush();

    }
}

