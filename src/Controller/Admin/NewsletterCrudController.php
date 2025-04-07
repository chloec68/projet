<?php

namespace App\Controller\Admin;

use App\Entity\Newsletter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class NewsletterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Newsletter::class;
    }

    public function configureActions(Actions $actions): Actions
    {   
        // création de l'action "send" avec le label associé "envoyer"
        // assignation de l'action à la variable $sendNewsletterAction
        $sendNewsletterAction = Action::new('send','Envoyer')
        // lie l'action à la route "send-newsletter" (méthode du HomeController)
        // 
        ->linkToRoute('send-newsletter', function ($newsletter) {
            return [
                'idNewsletter' => $newsletter->getId()
            ];
        })
        //ajout de l'icône
        ->setIcon('fa fa-paper-plane')
        //ajour classe CSS
        ->addCssClass('btn btn-info');
        // retourne l'objet Actions modifié 
        return $actions
        //ajoute de l'action à la page index du panneau admin
        ->add(Crud::PAGE_INDEX, $sendNewsletterAction)
        //désactive l'action modifier de la page index
        ->disable('edit')
        //modifie l'îcone et le libellé de l'action "créer"
        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon('fa fa-plus')->setLabel("Créer Newsletter");
        });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('newsLetterDate')
                ->renderAsNativeWidget(true)
                ->setFormTypeOption('data', new \DateTime())
                ->setRequired(true)
                ->setLabel('Date')
                ->setFormat('dd.MM.yyyy'),

            TextEditorField::new('newsLetterContent')->setLabel('Contenu de la Newsletter')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud    
            ->setPageTitle('index','Newsletter')
            ->setPageTitle('new','Créer une newsletter');
    }

}
