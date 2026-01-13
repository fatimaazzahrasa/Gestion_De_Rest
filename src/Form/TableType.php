<?php

namespace App\Form;

use App\Entity\Table;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la table',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Table 1'
                ]       
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacité (Places)',
                'attr' => ['class' => 'form-control', 'min' => 1]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut de la table',
                'choices' => [
                    'Libre' => 'libre',
                    'Occupée' => 'occupée',
                    'Réservée' => 'réservée'
                ],
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Table::class,
        ]);
    }
}
