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

        // BEERS SALES 
        $category = 1 ;
        $salesOfYearBeers = [];

        for($i=0;$i<12;$i++){
            $salesOfMonth = $productRepository->salesByMonth($category,$month[$i]);
            $salesOfYearBeers[] = $salesOfMonth ;
        }

        dd($salesOfYearBeers);

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
        $category = 2 ; 
        $salesOfYearGoodies = [];
        for($i=0;$i<12;$i++){
            $salesOfMonth = $productRepository->salesByMonth($category,$month[$i]);
            $salesOfYearGoodies[] = $salesOfMonth ;
        }

        $saleOfMarchGoodies = $productRepository->salesByMonth($category, $march);

        return $this->render('/admin/charts/products-chart.html.twig',[
            'salesOfYearBeers' => $salesOfYearBeers,
            'salesOfYearGoodies' => $salesOfYearGoodies
        ]);
    }
}