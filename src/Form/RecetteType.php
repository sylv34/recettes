<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recette;
use App\Entity\Season;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('cookingTime', TimeType::class, [
                'label' => 'Temps de cuisson',
            ])
            ->add('preparationTime', TimeType::class, [
                'label' => 'Temps de préparation',
            ])
            ->add('nbPerson', IntegerType::class, [
                'label' => 'Nombre de personnes'
            ])
            ->add('season', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'name',
                'label' => 'Saison',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Plat express, familiale...'
                ],
                'required' => false
            ])
            ->add('Enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
