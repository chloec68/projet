<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class StatsController extends AbstractController
{
    #[Route('/admin/charts', name:'admin_charts_products-chart')]
    public function productsChart()
    {
        return $this->render('/admin/charts/products-chart.html.twig');
    }
}