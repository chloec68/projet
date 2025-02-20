<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class IdentificationFormType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $builder
        ->add('orderUserFirstName', TextType::class,[
            'attr' => [
                'placeholder' => 'Prénom',
                'class'=>'custom-input',
            ]
        ])
        
        ->add('orderUserLastName', TextType::class,[
            'attr' => [
                'placeholder' => 'Nom',
                'class'=>'custom-input'
            ]
            ])

        ->add('orderEmail', EmailType::class, [
            'attr'=> [
                'placeholder'=>'Email',
                'class'=>'custom-input'
            ]
        ])

        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'Je certifie avoir 18 ans',
                ])
            ]
        ]);

        // ->add('Continuer', SubmitType::class, [
        //     'attr'=>[
        //         'class' => 'identification-btn btn',
        //     ],
        // ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'data_class' => Order::class,
        ]);
    }

}