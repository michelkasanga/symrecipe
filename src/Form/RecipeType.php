<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use  Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class RecipeType extends AbstractType
{
    private $token;
    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name'  ,TextType::class, [
                'attr' => [
                    'class' =>'form-control',
                    'minlength' => '2',
                    'maxlength' => '50'
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Length(['min'=>2, 'max'=>50]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('time', IntegerType::class, [
                'attr' =>[
                    'class' => 'form-control',
                    'minlength' => 1,
                    'maxlength' => 1440
                ],
                'label' => 'Temps(en minutes)',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [ 
                    new Assert\Positive(),
                   new Assert\LessThan(1440)
                ]
            ])
            ->add('nbPeople' , IntegerType::class, [
                'attr' =>[
                    'class' => 'form-control',
                    'minlength' => 1,
                    'maxlength' => 50
                ],
                'label' => 'Nombre de personnes',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [ 
                    new Assert\Positive(),
                   new Assert\LessThan(51)
                ]
            ])
            ->add('difficulty', RangeType::class, [
                'attr' =>[
                    'class' => 'form-range',
                    'min' => "1",
                    'max' => "5",
                    'step'=>"1",
                    //  class="form-range" min="0" max="5" step="0.5"
                ],
                'label' => 'Difficulte',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [ 
                    new Assert\Positive(),
                   new Assert\LessThan(5)
                ]
            ]  )
            ->add('description' , TextareaType::class, [
                'attr' =>[
                    'class' => 'form-control',
                    'minlength' => '5'
                    // 'maxlength' => '300'
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [ 
                    new Assert\NotBlank()
                ] 
            ] )
            ->add('price' , MoneyType::class,  [
                'attr' => [
                    'class' =>'form-control',
                    
                ],
                'label' => 'Prix',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [ 
                    new Assert\Positive(),
                   new Assert\LessThan(1001)
                ]
                  
            ])
            ->add('isFavorite'  , CheckboxType::class,  [
         
                 'required'=>false,
                'label' => 'Favoris  ?',
                
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
                'attr' => [
                    'class' =>'form-check-input',
                    'role' => 'switch',
                    
                ],   
                'constraints' => [ 
                    new Assert\NotNull()
                ]
                  
            ])

            ->add('isPublic'  , CheckboxType::class,  [
         
                'required'=>false,
               'label' => 'Public  ?',
               
               'label_attr' => [
                   'class' => 'form-check-label '
               ],
               'attr' => [
                   'class' =>'form-check-input',
                   'role' => 'switch',
                   
               ],   
               'constraints' => [ 
                   new Assert\NotNull()
               ]
                 
           ])
             ->add('ingredients',  EntityType::class, // array(
            //     'class' => Ingredient::class,

            //     'label' => 'Les ingredients',
            //     'label_attr' => [
            //         'class' => 'form-label mt-4 '
            //     ],
               
            //        'multiple' => true,
            //         'expanded' => true,
            //     'choice_attr' => function($val, $key, $index) {
            //         return array(
            //             'required' => false
            //         ); 
            //     }
                
            // )

            [
                'class'=> Ingredient::class,
                'query_builder'=> function (IngredientRepository $r){
                    return $r->createQueryBuilder('i')
                        ->where('i.user = :user')
                        ->orderBy('i.name', 'ASC')
                        ->setParameter('user', $this->token->getToken()->getUser());
                },
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name'
            ]

           ) 
            ->add('submit'  ,SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-primary mt-4'
                ],
                'label'=>'creer ma recette '
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
