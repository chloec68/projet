<?php
namespace App\Controller\Admin;

use DateTime;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class StatsController extends AbstractController
{
    #[Route('/admin/charts', name:'admin_charts_products-chart')]
    public function productsCharts(ProductRepository $productRepository)
    {   
        $january = DateTime::createFromFormat('m-Y','1-'.date('Y'));
        $february = \DateTime::createFromFormat('m-Y','2-'.date('Y'));
        $march = \DateTime::createFromFormat('m-Y','3-'.date('Y'));
        $april = \DateTime::createFromFormat('m-Y','4-'.date('Y'));
        $may = \DateTime::createFromFormat('m-Y','5-'.date('Y'));
        $june = \DateTime::createFromFormat('m-Y','6-'.date('Y'));
        $july = \DateTime::createFromFormat('m-Y','7-'.date('Y'));
        $august = \DateTime::createFromFormat('m-Y','8-'.date('Y'));
        $september = \DateTime::createFromFormat('m-Y','9-'.date('Y'));
        $october = \DateTime::createFromFormat('m-Y','10-'.date('Y'));
        $november = \DateTime::createFromFormat('m-Y','11-'.date('Y'));
        $december = \DateTime::createFromFormat('m-Y','12-'.date('Y'));

        $month = [$january,$february,$march,$april,$may,$june,$july,$august,$september,$october,$november,$december];
        $labels = ["January","February","March","April","May","June","July","August","September","October","November","December"];
   
        $yearlyProductSales = [];
        $monthlyProductData = [];  
    
        for ($i = 0; $i < 12; $i++) {
            $monthlySales = $productRepository->salesByMonth($month[$i]);
            $monthlyProductSales = [];
    
            foreach ($monthlySales as $monthlySale) {
                $productName = $monthlySale['productName'];
                $quantity = $monthlySale['quantity'];
            
                if (isset($monthlyProductSales[$productName])) {
                    $monthlyProductSales[$productName] += $quantity;
                } else {
                    $monthlyProductSales[$productName] = $quantity;
                }
            }
    
            $yearlyProductSales[] = $monthlyProductSales;
            $monthlyProductData[] = $monthlyProductSales;
        }
    
        return $this->render('admin/charts/products-chart.html.twig', [
            'labels' => $labels,
            'monthlyProductData' => $monthlyProductData,
        ]);
    }
}

