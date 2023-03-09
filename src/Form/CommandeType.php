<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Name:',
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Address:',
            ])
            ->add('email', TextType::class, [
                'label' => 'email:',
            ])
            ->add('city', TextType::class, [
                'label' => 'City:',
            ])
            ->add('tel', TextType::class, [
                'label' => 'Phone Number:',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'lastname:',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Confirm Order',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
