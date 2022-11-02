<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Ingredient;

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

class RecipeType extends AbstractType
{
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
                    'class' => 'form-control-range',
                    'minlength' => 1,
                    'maxlength' => 5,
                    'step'=>1,
                   
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
                'attr' => [
                    'class' =>' .btn-group-toggle form-check',
                
                    
                ],   
                 'required'=>false,
                'label' => 'Favoris  ?',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [ 
                    new Assert\NotNull()
                ]
                  
            ])
            ->add('ingredients', EntityType::class, array(
                'class' => Ingredient::class,

                'label' => 'Les ingredients',
                'label_attr' => [
                    'class' => 'form-label mt-4 bold'
                ],

                'expanded' => true,
                'multiple' => true,
                'choice_attr' => function($val, $key, $index) {
                    return array(
                        'required' => false
                    );
                }
                
            )
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
