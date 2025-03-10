<?php

namespace App\Repository;

use App\Entity\Product;
use App\Model\SearchDataBeers;
use App\Model\SearchDataGoodies;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    public function findBeers($idCategory,$isDeleted):array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :idCategory')
            ->andWhere('p.isDeleted = :isDeleted')
            ->setParameter('idCategory', $idCategory)
            ->setParameter('isDeleted', $isDeleted)
            ->getQuery()
            ->getResult();
    }

    //FIND ALL PRODUCTS THAT BELONG TO GOODIES CATEGORY
    public function findGoodies($idCategory,$isDeleted):array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :idCategory')
            ->andWhere('p.isDeleted = :isDeleted')
            ->setParameter('idCategory', $idCategory)
            ->setParameter('isDeleted', $isDeleted)
            ->getQuery()
            ->getResult();
    }

    //SEARCH BAR (BEERS)
    public function searchProductBeer(SearchDataBeers $searchData):array
    {
        $type = $searchData->getType();    
        $color = $searchData->getColor();  
        $name = $searchData->getName();
        $isPermanent = $searchData->getIsPermanent();   
        $category = $searchData->getCategory();

        $result = $this->createQueryBuilder('p')
            ->andWhere('p.category = :idCategory')   
            ->setParameter('idCategory', $category);

        if (!empty($type)){
            $result = $result
                ->andWhere('p.type = :type')
                ->setParameter('type', $type);
        }

        if(!empty($color)){
            $result = $result
                ->andWhere('p.productColor = :productColor')
                ->setParameter('productColor', $color);
        }

        if(!empty($name)){
            $result = $result
                ->andWhere('p.productName LIKE :productName')
                ->setParameter('productName', '%' . $name . '%');
        }

        if(isset($isPermanent) ){
            $result = $result
                ->andWhere('p.isPermanent = :isPermanent')
                ->setParameter('isPermanent', $isPermanent);
        }

        $result = $result
            ->orderBy('p.productName','ASC')
            ->getQuery()
            ->getResult();
        
        return $result;
        
    }

    // FIND PERMANENT/EPHEMERAL BEERS (DISPLAY ON HOME PAGE + METHOD USED IN HOME CONTROLLER)
       public function findByPermanency($permanency,$category): array
       {
           return $this->createQueryBuilder('p')
                ->andWhere('p.category = :idCategory')
                ->andWhere('p.isPermanent = :isPermanent')
                ->setParameter('idCategory', $category)
                ->setParameter('isPermanent', $permanency)
                ->orderBy('p.productName', 'ASC')
                ->getQuery()
                ->getResult();
       }
    
       //SEARCH BAR (GOODIES)
       public function searchProductGoodie(SearchDataGoodies $searchData):array
       {
           $productGender = $searchData->getProductGender();    
           $color = $searchData->getColor();  
           $name = $searchData->getName();
           $size = $searchData->getSize();   
           $category = $searchData->getCategory();
   
           $result = $this->createQueryBuilder('p')
               ->andWhere('p.category = :idCategory')   
               ->setParameter('idCategory', $category);
   
           if (!empty($productGender)){
               $result = $result
                   ->andWhere('p.productGender = :productGender')
                   ->setParameter('productGender', $productGender);
           }
   
           if(!empty($color)){
               $result = $result
                   ->andWhere('p.productColor = :productColor')
                   ->setParameter('productColor', $color);
           }
   
           if(!empty($name)){
               $result = $result
                   ->andWhere('p.productName LIKE :productName')
                   ->setParameter('productName', '%' . $name . '%');
           }

           if(!empty($size)){
            $result = $result
                ->andWhere('p.size = :size')
                ->setParameter('size', $size);
        }
   
           $result = $result
               ->orderBy('p.productName','ASC')
               ->getQuery()
               ->getResult();
           
           return $result;
           
       }
   


    //FIND ALL BEERS OF A CERTAIN TYPE
        // DQL => SELECT p FROM App\Entity\Beer p WHERE p.category = :idCategory AND p.type = :type
    // public function findBeersByType($type):array
    // {
    //     return $this->createQueryBuilder('p')
    //         ->andWhere('p.category = :idCategory')
    //         ->andWhere('p.type = :type')
    //         ->setParameter('idCategory', '1')
    //         ->setParameter('type', $type)
    //         ->orderBy('p.productName','ASC')
    //         ->getQuery()
    //         ->getResult();
    // }

    // FIND ALL BEERS OF A CERTAIN COLOR 
        // DQL => SELECT p FROM App\Entity\Beer p WHERE p.color = :color
    // public function findBeersByColor($color):array
    // {
    //     return $this->createQueryBuilder('p')
    //         ->andWhere('p.category = :idCategory')
    //         ->andWhere('p.productColor = :productColor')
    //         ->setParameter('idCategory', '1')
    //         ->setParameter('productColor', $color)
    //         ->orderBy('p.productName','ASC')
    //         ->getQuery()
    //         ->getResult();
    // }
       
    //      public function findOneByName($name): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //             ->andWhere('p.category = :idCategory')
    //             ->andWhere('p.productName LIKE :productName')
    //             ->setParameter('idCategory', '1')
    //             ->setParameter('productName', $name)
    //             ->getQuery()
    //             ->getOneOrNullResult()
    //        ;
    //    }

        //DQL => 'SELECT * FROM product WHERE product_name LIKE "%'.$name.'%" 
    //    public function findOneByName($name): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //             ->andWhere('p.category = :idCategory')
    //             ->andWhere('p.productName LIKE :productName')
    //             ->setParameter('idCategory', '1')
    //             ->setParameter('productName', '%'.$name.'%')
    //             ->getQuery()
    //             ->getOneOrNullResult()
    //        ;
    //    }



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
