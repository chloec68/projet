<?php

namespace App\Form;

use App\Entity\Type;
use App\Model\SearchDataBeers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchFormBeers extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => false, //on ne veut pas de label
                'required' => false, //on n'est pas obligé de faire une recherhe 
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
                ])
            
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'typeName',
                'label' => false,
                'required' => false,
                'expanded' => true, //sinon on obtient une liste déroulante
                'multiple' => false, 
                'placeholder' => false,
            ])
            
            ->add('color', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => false,
                'expanded' => true, //sinon on obtient une liste déroulante
                'multiple' => false, 
                'choices' => [
                    'Blonde' => 'blonde',
                    'Blanche' => 'blanche',
                    'Ambrée' => 'ambree',
                    'Rousse' => 'rousse',
                    'Black' =>'black',
                    'Rouge' => 'rouge'
                ],
            ])

            ->add('isPermanent', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => false,
                'expanded' => true, //sinon on obtient une liste déroulante
                'multiple' => false, 
                'choices' => [
                    'Permanente' => true,
                    'Ephémère' => false,
                ],
            ])

            ->add('chercher', SubmitType::class, [
                'attr'=>[
                    'class' => 'search-btn btn'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchDataBeers::class,
            'method' => 'GET', // pour que l'utilisateur puisse partager ses recherches 
            'csrf_protection' => false // "pour ne pas avoir de problème de cross-scripting"
        ]);
    }

    public function getBlockPrefix() // pour garder une URL la plus propre possible
    {
        return '';
    }

}
