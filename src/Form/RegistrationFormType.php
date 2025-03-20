<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('captcha', Recaptcha3Type::class, [
            'constraints' => new Recaptcha3(),
            'action_name' => 'register',
            // 'script_nonce_csp' => $nonceCSP, //optionnel //The script_nonce_csp parameter is optional. You must use the same nonce as in your Content-Security Policy header.
            'locale' => 'fr',
        ])
            ->add('email', EmailType::class, [
                'attr'=> [
                    'placeholder'=>'Email',
                    'class'=>'custom-input custom-input-register'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                // 'constraints' => [
                //     new IsTrue([
                //         'message' => 'J\'accepte les termes et conditions',
                //     ]),
                // ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe doit être le même',
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr'=>['class' => 'custom-input custom-input-register','placeholder'=>'Mot de passe']
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr'=>['class' => 'custom-input custom-input-register','placeholder'=>'Confirmer le mot de passe']
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/',
                        'message' => 'Le mot de passe doit contenir au moins 12 caractères, dont un chiffre, une majuscule et un caractère spécial',
                    ]),
                    new Length([
                        // 'min' => 12,
                        // 'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,    // max length allowed by Symfony for security reasons
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
