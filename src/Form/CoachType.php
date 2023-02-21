<?php

namespace App\Form;

use App\Entity\Coach;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Regex;

class CoachType extends AbstractType
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
            ->add('email',TextType::class, [

                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern'=>'/^[a-zA-Z]+$/',
                        'message'=>'le nom doit contenir que des alphabets'
                    
                ]),

                ]
                ])
            ->add('telephone',NumberType::class,[
                
                'constraints' => [
                    new NotBlank([
                        'message' => 'ce champ ne doit pas etre vide',
                    ]),
                    
                    new Regex([
                        'pattern'=>'/^[0-9]+$/',
                        'message'=>'veuillez entrer que des nombres'
                    
                ]),
            ]])
            ->add('academy')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coach::class,
        ]);
    }
}
