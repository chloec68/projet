<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PictureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Picture::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions

        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon('fa fa-plus')->setLabel("Ajouter une photo");
        });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('pictureName')->setLabel('Chemin/Nom'),

            // ImageField::new('pictureName')
            // ->setFormTypeOption('multiple',true)
            // ->setUploadDir('public/img')
            // // ->setBasePath('public/img')
            // ->setLabel('Ajouter photo(s) produit')
            // // >setFileConstraints(new Image(maxSize: '100k'))
            // // ->setUploadedFileNamePattern('[year]/[month]/[day]/[slug]-[contenthash].[extension]');
            // ->setRequired(false),

        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud    
            ->setPageTitle('index','Photos produit')
            ->setPageTitle('edit','Modifier')
            ->setPageTitle('new','Ajouter une nouvelle photo');
    }
}
