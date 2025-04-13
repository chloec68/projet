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
            ->add('name', TextType::class,[ //ajout d'un champ de type texte
                'label' => false, //ne pas ajouter de label
                'required' => false, //le champ ne doit pas obligatoirement être rempli 
                'attr' => [
                    'placeholder' => 'Rechercher' //indication du texte de remplacement
                ]
                ])
            ->add('type', EntityType::class, [ //le champ EntityType permet de lier un champ à une entité 
                'class' => Type::class, //la classe Type représente une entité de la base de données 
                'choice_label' => 'typeName', //propriété de l'entité à afficher 
                'label' => false, // ne pas ajouter de label
                'required' => false, // le champ ne doit pas obligatoirement contenir une baleur 
                'expanded' => true, // affichage sous forme de boutons (et non pas sous forme de liste déroulante)
                'multiple' => false, // pas de choix multiple possible
                'placeholder' => false, // pas d'indication de texte de remplacement 
            ])
            ->add('color', ChoiceType::class, [ //champ de formulaire qui permet à l'utilisateur de choisir parmi une liste prédéfinie
                'label' => false, //ne pas afficher de label
                'required' => false, //le champs n'est pas obligatoire 
                'placeholder' => false, // pas d'indication de texte de remplacement 
                'expanded' => true, //pour obtenir des boutous (pas de liste déroulante)
                'multiple' => false, //pas de choix multiple possible 
                'choices' => [ //la liste de valeurs parmi lesquelles l'utilisatuer peut choisir 
                    'Blonde' => 'blonde',
                    'Blanche' => 'blanche',
                    'Ambrée' => 'ambree',
                    'Rousse' => 'rousse',
                    'Black' =>'black',
                    'Rouge' => 'rouge'
                ],
            ])
            ->add('isPermanent', ChoiceType::class, [ //champ de formulaire qui permet à l'utilisateur de choisir parmi une liste prédéfinie
                'label' => false, //ne pas afficher de label
                'required' => false, //le champs n'est pas obligatoire 
                'placeholder' => false,// pas d'indication de texte de remplacement 
                'expanded' => true, //sinon on obtient une liste déroulante
                'multiple' => false, //pas de choix multiple possible 
                'choices' => [ //la liste de valeurs parmi lesquelles l'utilisatuer peut choisir 
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
            'data_class' => SearchDataBeers::class, //spécifie la classe à laquelle les données soumises doivent être liées 
            'method' => 'GET', // permet de rendre les données visibles dans l'URL, pour que l'utilisateur puisse partager ses recherches 
        ]);
    }

}
