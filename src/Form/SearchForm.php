<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;




class SearchForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class,[
                'label' => false, //on ne veut pas de label
                'required' => false, //on n'est pas obligé de faire une recherhe 
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
                ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Product::class,
            'method' => 'GET', // pour que l'utilisateur puisse partager ses recherches 
            'csrf_protection' => false // "pour ne pas avoir de problème de cross-scripting"
        ]);
    }

    public function getBlockPrefix() // pour garder une URL la plus propre possible
    {
        return '';
    }

}
