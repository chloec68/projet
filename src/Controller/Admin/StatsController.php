<?php
namespace App\Controller\Admin;

use DateTime;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class StatsController extends AbstractController
{
    #[Route('/admin/charts', name:'admin_charts_products-chart')]
    public function productsChart(ProductRepository $productRepository)
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

        // BEERS SALES 
        $category = 1 ;
        $yearlyProductSales = [];

        for($i=0;$i<12;$i++){
            $monthlySales = $productRepository->salesByMonth($category,$month[$i]);
            $monthlyProductSales = [];

            foreach ($monthlySales as $monthlySale){
              $productName = $monthlySale['productName'];
              $quantity = $monthlySale['quantity'];

              if(isset($monthlyProductSales[$productName])){
                $monthlyProductSales[$productName] += $quantity;
              }else{
                $monthlyProductSales[$productName] = $quantity;
              }
            }
            $yearlyProductSales[] = $monthlyProductSales;
        }

        // dd($yearlyProductSales);

        $datasets = []; 
        $productNames = []; 

        foreach($yearlyProductSales as $monthlySales){
            foreach($monthlySales as $productName => $quantity){
                // if (!in_array($productName, $productNames)) {
                    $productNames[] = $productName; 
                // }
            }
        }

        foreach ($productNames as $productName){
            $productData = [];

            foreach ($yearlyProductSales as $monthlySales){
                $productData[] = isset($monthlySales[$productName]) ? $monthlySales[$productName] : 0;
            }
        }

        $datasets[] = [
            'label' => $productName,
            'data' => $productData,  // The sales quantities for each month
            'backgroundColor' => 'rgba(75, 192, 192, 0.2)', // Customize the color
            'borderColor' => 'rgba(75, 192, 192, 1)',
            'borderWidth' => 1,
        ];

        dd($datasets);
        return $this->render('/admin/charts/products-chart.html.twig',[
            'datasets' => $datasets,
            'labels' => $labels
        ]);
    }
}


        // $salesOfJanuaryBeers = $productRepository->salesByMonth($category,$january);
        // $salesOfFebruaryBeers = $productRepository->salesByMonth($category,$february);
        // $salesOfMarchBeers = $productRepository->salesByMonth($category,$march);
        // $salesOfAprilBeers = $productRepository->salesByMonth($category,$april);
        // $salesOfMayBeers = $productRepository->salesByMonth($category,$may);
        // $salesOfJuneBeers = $productRepository->salesByMonth($category,$june);
        // $salesOfJulyBeers = $productRepository->salesByMonth($category,$july);
        // $salesOfAugustBeers = $productRepository->salesByMonth($category,$august);
        // $salesOfSeptemberBeers = $productRepository->salesByMonth($category,$september);
        // $salesOfOctoberBeers = $productRepository->salesByMonth($category,$october);
        // $salesOfNovemberBeers = $productRepository->salesByMonth($category,$november);
        // $salesOfDecemberBeers = $productRepository->salesByMonth($category,$december);


        //GOODIES SALES 
        // $category = 2 ; 
        // $salesOfYearGoodies = [];
        // for($i=0;$i<12;$i++){
        //     $salesOfMonth = $productRepository->salesByMonth($category,$month[$i]);
        //     $salesOfYearGoodies[] = $salesOfMonth ;
        // }

        // $saleOfMarchGoodies = $productRepository->salesByMonth($category, $march);
