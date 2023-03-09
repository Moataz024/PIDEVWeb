<?php

namespace App\Form;
use App\Entity\Equipment;
use App\Repository\EquipmentRepository;
use App\Entity\Terrain;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateReservation', DateTimeType::class,
            [
                'date_widget' => 'single_text'
            ])
            ->add('startTime', DateTimeType::class,
            [
                'date_widget' => 'single_text'
            ])
            ->add('endTime', DateTimeType::class,
            [
                'date_widget' => 'single_text'
            ])
            ->add('nbPerson')
            ->add('equipments', EntityType::class, [
                'class' => Equipment::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function (Equipment $equipment) {
                    return sprintf('%s (%.2f DT)', $equipment->getName(), $equipment->getPrice());
                },
                'query_builder' => function (EquipmentRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.name', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
