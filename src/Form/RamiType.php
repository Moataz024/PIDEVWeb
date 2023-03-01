<?php

namespace App\Form;

use App\Entity\Rami;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RamiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $sportsCategories = [
            'Football' => 'Football',
            'Basketball' => 'Basketball',
            'Tennis' => 'Tennis',
            'Swimming' => 'Swimming',
        ];
        $builder
            ->add('name')
            ->add('age')
            ->add('field', ChoiceType::class, [
                'choices' => $sportsCategories,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rami::class,
        ]);
    }
}
