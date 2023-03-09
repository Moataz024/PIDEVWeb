<?php

namespace App\Form;

use App\Entity\Coach;
use App\Entity\Application;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
use Symfony\Component\Validator\Constraints\GreaterThan;




class ApplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Appname', TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s]+$/',
                        'message' => 'The name can only contain alphabets'
                    ]),
                   
                ],
            ])
            ->add('Appage', IntegerType::class,[
                'label' => 'Age',
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Age must be greater than or equal to 1',
                    ]),
                ],
            ]);  
            
    //         ->add('telephone',TelType::class,[
    //             'attr'=>[
    //                 'class'=>'form-control'  
    //             ],
    //             'constraints' => [
    //                 new NotBlank([
    //                     'message' => 'Please enter a phone number',
    //                 ]),
    //                 new Length([
    //                     'min' => 8,
    //                     'minMessage' => 'Your phone number have {{ limit }} characters',
    //                     // max length allowed by Symfony for security reasons
    //                     'max' => 8,
    //                 ]),
    //                 new Regex([
    //                     'pattern'=>'/^[0-9]+$/',
    //                     'message'=>'Your phone should be at least 8 characters'
                    
    //             ]),
    //         ]]);
                   
          ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
