<?php

namespace App\Controller\Admin;

use App\Entity\Bill;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BillCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bill::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud    
            ->setPageTitle('index','Facture')
            ->setPageTitle('new','Créer une facture')
            ->setPageTitle('edit','Modifier la commande');

    }
}
