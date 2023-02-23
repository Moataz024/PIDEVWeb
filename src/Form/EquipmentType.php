<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\Suppliers;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Regex;




class EquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [

                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern'=>'/^[a-zA-Z]+$/',
                        'message'=>'le nom doit contenir que des alphabets'
                    
                ]),

                ]
                ])
                
                ->add('image')


            ->add('adress',TextType::class, [

                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern'=>'/^[a-zA-Z0-9]+$/',
                        'message'=>"l'adresse est invalide"
                    
                ]),

                ]
                ])
            ->add('type',TextType::class, [

                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern'=>'/^[a-zA-Z]+$/',
                        'message'=>'le type doit contenir que des alphabets'
                    
                ]),

                ]
                ])
            ->add('quantity',NumberType::class,[
                
                'constraints' => [
                    new NotBlank([
                        'message' => 'ce champ ne doit pas etre vide',
                    ]),
                    
                    new Regex([
                        'pattern'=>'/^[0-9]+$/',
                        'message'=>'veuillez entrer que des nombres'
                    
                ]),
            ]])
            ->add('Price',TextType::class,[

                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern'=>'/^[0-9]+$/',
                        'message'=>'le type doit contenir que des alphabets'
                    
                ]),

                ]
            ])
            ->add('suppliers',EntityType::class,
            ['class'=>Suppliers::class,
            'choice_label'=>'name',
            'multiple'=>false
            ])
            
            ->add('category',EntityType::class,
            ['class'=>Category::class,
            'choice_label'=>'nom',
            'multiple'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipment::class,
        ]);
    }
}
