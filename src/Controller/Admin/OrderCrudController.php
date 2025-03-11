<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->disable('edit')
        ->disable('delete');
        
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('dateOfPlacement')
                ->setLabel('Date de commande')
                ->setFormat('dd.MM.yyyy'),
            
            TextField::new('orderReference')
                ->setLabel('Référence'),
            
            TextField::new('orderFullName')
                ->setLabel('Client (prénom, nom, email)'),
            
            BooleanField::new('orderIsCollected')
                ->setLabel('Retrait effectué')
                ->renderAsSwitch(true),
            
            TextField::new('orderTotal')
                ->setLabel('Total TTC (€)'),
            
            TextField::new('totalBeforeVat')
            ->setLabel('Total HT (€)'),
            
            TextField::new('billReference')
                ->setLabel('Référence facture'),
            
        ];
    }

}
