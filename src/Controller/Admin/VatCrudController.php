<?php

namespace App\Controller\Admin;

use App\Entity\Vat;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class VatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Vat::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('vatRate')->setLabel('TVA %'),

        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud    
            ->setPageTitle('index','TVA')
            ->setPageTitle('edit','Modifier un taux de TVA')
            ->setPageTitle('new','Ajouter un nouveau taux de TVA');
    }
 
}
