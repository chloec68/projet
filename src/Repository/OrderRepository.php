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

    // SELECT SUM(order.order_total) AS total
    // FROM `order`
    // WHERE DATE_FORMAT(order.date_of_placement, '%Y-%m') = '2025-03';

    //    /**
    //     * @return Order[] Returns an array of Order objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Order
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
