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

       public function findByIsCollected($isCollected): array
       {            

        //initialisation du QueryBuilder de Doctrine pour construire une requête
        //'o' est un alias de l'entité Order sur laquelle on effectue la requête 
           return $this->createQueryBuilder('o')
            //ajout d'une condition WHERE pour filtrer les résultats selon la valeur de la colonne "oderIsCollected"
            //utilisation d'une valeur de paramètre nommé ':isCollected' pour éviter les injection SQL
               ->andWhere('o.orderIsCollected = :isCollected')
            //liaison du paramètre ':isCollected' à la variable fournie 
            //Doctrine échappe automatiquement cette valeur 
               ->setParameter('isCollected', $isCollected)
            //ordonne les résultats dans l'ordre croissant selon la colonne 'dateOfPlacement'
               ->orderBy('o.dateOfPlacement', 'ASC')
            //exécute la requête construite
               ->getQuery()
            //retourne les résultats sous forme d'un tableau d'entités
               ->getResult()
           ;
        }

      //   public function findByDate($date): array
      //   {
      //    return $this->createQueryBuilder('o')
      //    ->andWhere('o.dateOfPlacement = :date')
      //    ->setParameter('date',$date)
      //    ->orderBy('o.dateOfPlacement','ASC')
      //    ->getQuert()
      //    ->getResults();
      //   }
}
