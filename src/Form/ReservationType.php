<?php
// Fichier : src/Form/ReservationType.php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On ne garde QUE les champs que le client doit remplir,
        // en utilisant TES noms de propriétés.
        $builder
            ->add('reservation_date', DateTimeType::class, [
                'label' => 'Date et heure de votre venue',
                'widget' => 'single_text', // Affiche un joli sélecteur de date/heure
                'attr' => [
                    // Empêche de réserver dans le passé
                    'min' => (new \DateTime())->format('Y-m-d H:i'),
                ],
            ])
            ->add('nbr_personne', IntegerType::class, [
                'label' => 'Pour combien de personnes ?',
                'attr' => [
                    'min' => 1 // On ne peut pas réserver pour 0 personne
                ]
            ])
            // On a supprimé 'status', 'customer', et 'table_res' car
            // c'est le contrôleur qui s'en chargera.
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
