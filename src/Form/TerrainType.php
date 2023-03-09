<?php

namespace App\Form;

use App\Entity\Terrain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType; 
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class TerrainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('capacity',)
            ->add('sportType', ChoiceType::class, [
                'choices' => [
                    'Football' => 'football',
                    'Handball' => 'handball',
                    'Basketball' => 'basketball',
                    'Volleyball' => 'volleyball',
                    'Baseball' => 'baseball',
                    'Tennis' => 'tennis',
                    'Golf' => 'golf',
                    // add more options here as needed
                ],
                'expanded' => false,
                'placeholder' => 'Choose a sport',
                'attr' => [
                    'class' => 'form-select', // add any Bootstrap or custom classes here
                ],
            ])
            ->add('rentPrice')
            ->add('disponibility')
            ->add('postalCode')
            ->add('roadName')
            ->add('roadNumber')
            ->add('city')
            ->add('country')
            ->add('imageFile', VichImageType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terrain::class,
        ]);
    }
}
