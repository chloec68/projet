<?php

namespace App\Controller\Admin;

use App\Entity\Bill;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class BillCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bill::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions

        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon('fa fa-plus')->setLabel("Nouvelle Facture");
        });
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud    
            ->setPageTitle('index','Facture')
            ->setPageTitle('new','Créer une facture')
            ->setPageTitle('edit','Modifier la commande');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('billReferenceNumber')
            ->setLabel('Référence facture'),

            TextField::new('billTotalVat')
            ->setLabel('Total TTC €'),

            TextField::new('billTotalBeforeVat')
            ->setLabel('Total HT €'),

            DateTimeField::new('billDate')
            ->setLabel('Date')
            ->setFormat('dd.MM.yyyy'),
        ];

    }
}
