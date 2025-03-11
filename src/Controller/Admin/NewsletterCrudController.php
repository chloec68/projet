<?php

namespace App\Controller\Admin;

use App\Entity\Newsletter;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
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


    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('newsLetterDate')
                ->renderAsNativeWidget(true)
                ->setLabel('Date')
                ->setFormat('dd.MM.yyyy'),

            TextEditorField::new('newsLetterContent')->setLabel('Contenu de la Newsletter')
        ];
    }

}
