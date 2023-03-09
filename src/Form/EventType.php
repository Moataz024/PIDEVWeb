<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\SponsorE;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EventType extends AbstractType
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
            ->add('nom')
            ->add('category', ChoiceType::class, [
                'choices' => $sportsCategories,
            ])
            ->add('organisateur')
            ->add('sponsors')
            ->add('lieu')
            ->add('description')
            //->add('sponsors',EntityType::class,['class'=>SponsorE::class,'choice_label'=>'name','multiple'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
