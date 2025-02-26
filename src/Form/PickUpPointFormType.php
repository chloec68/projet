<?php

namespace App\Form;

use App\Entity\Establishment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PickUpPointFormType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $builder
        ->add('establishmentName', EntityType::class, [
            'class' => Establishment::class,
            'choice_label' => 'establishmentName',
            'label' => false,
            'required' => false,
            'placeholder' => false,
            'expanded' => true, 
            'multiple' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST'
        ]);
    }
}