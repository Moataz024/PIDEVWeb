<?php

namespace App\Form;

use App\Entity\Coach;
use App\Entity\Academy;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\Length;


class CoachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [

                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern'=>'/^[a-zA-Z\s]+$/',
                        'message'=>'The name can only contain alphabets'
                    
                ]),

                ]
                ])
            ->add('email',EmailType::class, [
                'attr'=>[
                    'class'=>'form-control'  
                ],
                'constraints' => [
                    new NotBlank(),
                    new Email(),

                ]
                ])
            ->add('telephone',TelType::class,[
                'attr'=>[
                    'class'=>'form-control'  
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a phone number',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your phone number have {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 8,
                    ]),
                    new Regex([
                        'pattern'=>'/^[0-9]+$/',
                        'message'=>'This is not a valid phone number'
                    
                ]),
            ]])
            ->add('academy', EntityType::class, [
                'class' => Academy::class,
                'choice_label'=>'name',
                'multiple'=>false
            ]);            
          ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coach::class,
        ]);
    }
}
