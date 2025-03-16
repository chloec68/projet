<?php

namespace App\Form;

use App\Entity\Recipient;
use App\Entity\Newsletter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RecipientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipientEmail', EmailType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'mon-adresse@email.com']
                 
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'M\'inscrire',
                'attr' => ['class'=>'btn']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipient::class,
                'method' => 'POST'
        ]);
    }
}
