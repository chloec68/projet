<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function totalSalesByMonth($dateOfPlacement)
    {
        $year = $dateOfPlacement->format('Y');  //Extrait l'année de $dateOfPlacement
        $month = $dateOfPlacement->format('m'); //Extrait le mois de $dateOfPlacement
        $startDate = new \DateTime("$year-$month-01 00:00:00");
        $endDate = clone $startDate;
        $endDate->modify('last day of this month 23:59:59');

        $totalSales = $this->createQueryBuilder('o')
        ->select('SUM(o.orderTotal)')
        ->andWhere('o.dateOfPlacement BETWEEN :startDate AND :endDate')
        ->setParameter('startDate', $startDate)
        ->setParameter('endDate', $endDate)
        ->getQuery()
        ->getSingleScalarResult();

        if($totalSales === null){
            $totalSales = 0;
        }

        return $totalSales;
    }
}
