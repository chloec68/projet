<?php

namespace App\Controller\Admin;

use App\Entity\Vat;
use App\Entity\Bill;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\Newsletter;
use App\Controller\Admin\DashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;



// #[IsGranted('ROLE_ADMIN')]
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{   
    public function index(): Response
    {
        // return parent::index();

        return $this->render('/admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tableau de bord')
            ->setTranslationDomain('EasyAdmin')
            ->setDefaultColorScheme('dark');    
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');
        
        yield MenuItem::linkToCrud('Produits', 'fas fa-dragon', Product::class);

        yield MenuItem::linkToCrud('Photos produit', 'fas fa-box-archive', Picture::class);

        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);

        yield MenuItem::linkToCrud('TVA', 'fas fa-percent', Vat::class);

        yield MenuItem::linkToCrud('Newsletter', 'fas fa-envelope-open-text', Newsletter::class);

        yield MenuItem::linkToCrud('Commandes', 'fas fa-box-archive', Order::class);

        yield MenuItem::linkToCrud('Factures (BtoC)', 'fas fa-box-archive', Bill::class);

    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}

