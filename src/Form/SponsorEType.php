<?php

namespace App\Form;

use App\Entity\SponsorE;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SponsorEType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSponsor')
            ->add('emailSponsor')
            ->add('telSponsor')
            ->add('sponsoredEvents')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SponsorE::class,
        ]);
    }
}
