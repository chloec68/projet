<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // FIND ALL PRODUCTS THAT BELONG TO BEERS CATEGORY
    public function findBeers():array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :idCategory')
            ->setParameter('idCategory', '1')
            ->getQuery()
            ->getResult();
    }

    //FIND ALL PRODUCTS THAT BELONG TO GOODIES CATEGORY
    public function findGoodies():array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :idCategory')
            ->setParameter('idCategory', '2')
            ->getQuery()
            ->getResult();
    }

    //FIND ALL BEERS OF A CERTAIN TYPE
        // DQL => SELECT p FROM App\Entity\Beer p WHERE p.category = :idCategory AND p.type = :type
    public function findBeersByType($type):array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :idCategory')
            ->andWhere('p.type = :type')
            ->setParameter('idCategory', '1')
            ->setParameter('type', $type)
            ->orderBy('p.productName','ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
