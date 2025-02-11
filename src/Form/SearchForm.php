<?php

namespace App\Form;

use App\Model\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchForm extends AbstractType
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
            
            ->add('type', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'expanded' => true, //sinon on obtient une liste déroulante
                'multiple' => false, 
                'placeholder' => false,
                'choices' => [
                    'IPA' => 'ipa',
                    'Pale Ale' => 'paleale',
                    'DDH' => 'ddh',
                    'NEIPA' => 'neipa',
                    'Witbier' =>'witbier',
                    'Pilsner' => 'pilsner',
                    'Stout' => 'stout'
                ],
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

            ->add('chercher', SubmitType::class, [
                'attr'=>[
                    'class' => 'search-btn btn'
                ]
                ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET', // pour que l'utilisateur puisse partager ses recherches 
            'csrf_protection' => false // "pour ne pas avoir de problème de cross-scripting"
        ]);
    }

    public function getBlockPrefix() // pour garder une URL la plus propre possible
    {
        return '';
    }

}
