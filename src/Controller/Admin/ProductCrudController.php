<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->disable('delete')

        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon('fa fa-plus')->setLabel("Nouveau produit");
        });
    }
    
    public function configureFields(string $pageName): iterable
    {
        $fields = [ 
            TextField::new('productName')->setLabel('Désignation du produit'),
            AssociationField::new('category')
                ->setLabel('Catégorie')
                ->setFormTypeOption('choice_label', 'categoryName') 
                ->setHelp('Sélectionner une catégorie pour ce produit')
                ->setFormTypeOptions([
                    'placeholder' => 'Choisir une catégorie', 
                    'label' => 'Catégorie'
                ]),
            AssociationField::new('type')
                ->setFormTypeOption('choice_label', 'typeName') 
                ->setHelp('Sélectionner un type pour ce produit')
                ->setFormTypeOptions([
                    'placeholder' => 'Choisir un type', 
                    'label' => 'Type'
                ]),
            TextField::new('productPrice')->setLabel('Prix en €'),
            TextField::new('productAlcoholLevel')->setLabel('Taux d\'alcool'),
            TextField::new('productVolume')->setLabel('Volume'),
            TextField::new('productColor')->setLabel('Couleur'),
            TextEditorField::new('productDescription')->setLabel('Description'),
            NumberField::new('productStock')->setLabel('Stock'),
            AssociationField::new('vat')
                ->setFormTypeOption('choice_label', 'vatRate') 
                ->setLabel('TVA')
                ->setHelp('Sélectionner un taux de TVA pour ce produit')
                ->setFormTypeOptions([
                    'placeholder' => 'Choisir un taux de TVA', 
                    'label' => 'Taux de TVA'
                ]),
            BooleanField::new('isDeleted')->setLabel('désactiver')
                    ->renderAsSwitch(true),
        ];
        
        return $fields;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud    
            ->setPageTitle('index','Produits')
            ->setPageTitle('edit','Modifier le produit')
            ->setPageTitle('new','Créer un nouveau produit');
    }
    


}
