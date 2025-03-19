<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
        ->disable('delete')

        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon('fa fa-plus')->setLabel("Nouvelle commande");
        });
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
            
            AssociationField::new('orderProducts','Produit(s)')
            ->onlyOnIndex()
            ->setFormTypeOptions([
                'by_reference' => false,
            ]),
            
            AssociationField::new('orderProduct','Produit(s)')
            ->setTemplatePath('admin/fields/products_list.html.twig')
            ->onlyOnDetail()
            ->setFormTypeOptions([
                'by_reference' => false,
               
            ]),
            
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud    
            ->setPageTitle('index','Commande')
            ->setPageTitle('edit','Modifier la commande');
    }


    #[Route('/admin/pendingOrders', name: 'admin_pendingOrders')]
    public function pendingOrders(OrderRepository $orderRepository):Response
    {   
        $isCollected = 0;
        $pendingOrders = $orderRepository->findByIsCollected($isCollected);


        return $this->render('/admin/fields/pending-orders_list.html.twig',[
            'pendingOrders' => $pendingOrders,
        ]);
    }

}
