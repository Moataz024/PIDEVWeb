<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\SponsorE;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('category')
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
