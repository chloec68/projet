<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->disable('delete')

        ->disable('new');
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            TextField::new('email')->setLabel('Adresse Email'),
            TextField::new('password')->setLabel('Mot de passe'),
            BooleanField::new('isVerified')->setLabel('Email vérifié')->renderAsSwitch(true),
            BooleanField::new('isDeleted')->setLabel('Compte supprimé')->renderAsSwitch(false),
            ArrayField::new('roles')
            ->setLabel('roles')
        ];

        return $fields;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud    
            ->setPageTitle('index','Utilisateurs')
            ->setPageTitle('edit','Modifier des informations utilisateurs')
            ->setPageTitle('new','Ajouter un nouvel utilisateur');
    }
  
}
