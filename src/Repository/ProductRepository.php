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

        // CHARTS 

       public function salesByMonth($dateOfPlacement) {

        $year = $dateOfPlacement->format('Y');  //Extrait l'année de $dateOfPlacement
        $month = $dateOfPlacement->format('m'); //Extrait le mois de $dateOfPlacement
        $startDate = new \DateTime("$year-$month-01 00:00:00");
        $endDate = clone $startDate;
        $endDate->modify('last day of this month 23:59:59'); // Relative Date Modifier => la chaîne de caractères 'last day of this month' est une instruction
        // PHP 

        return $this->createQueryBuilder('p')
            ->select('p.productName','op.quantity')
            ->innerJoin('p.orderProducts', 'op')  
            ->innerJoin('op.appOrder', 'o')  
            // ->andWhere('p.category = :category')
            ->andWhere('o.dateOfPlacement BETWEEN :startDate AND :endDate')
            // ->setParameter('category', $category)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('p.productName', 'ASC')
            ->getQuery()
            ->getResult();
    }

            // SELECT product.product_name,order_product.quantity FROM product 
            // INNER JOIN order_product ON product.id = order_product.app_product_id
            // INNER JOIN `order` ON order_product.app_order_id = `order`.id
            // WHERE product.category_id = 1 AND CONCAT(YEAR(`order`.date_of_placement),'-',MONTH(`order`.date_of_placement)) = '2025-3'

}
