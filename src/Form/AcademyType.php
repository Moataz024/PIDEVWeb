<?php


namespace App\Form;

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


class AcademyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $sportsCategories = [
            'Football' => 'Football',
            'Basketball' => 'Basketball',
            'Tennis' => 'Tennis',
            'Volleyball' => 'Volleyball',
            'Handball' => 'Handball',
        ];
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
            ->add('category', ChoiceType::class, [
                'choices' => $sportsCategories,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Academy::class,
        ]);
    }
}
