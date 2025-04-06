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
        //Récupération des différents critères de recherche depuis l'objet SearchDataBeers et assignation de leur valeur à une variable 
        $type = $searchData->getType();    
        $color = $searchData->getColor();  
        $name = $searchData->getName();
        $isPermanent = $searchData->getIsPermanent();   
        $category = $searchData->getCategory();
        //Création d'un QueryBuilder pour contruire une requête DQL 
        $result = $this->createQueryBuilder('p')
            //La requête comprend une première condition : la catégorie à laquelle le produit appartient
            ->andWhere('p.category = :idCategory') // filtre les produits selon leur catégorie 
            ->setParameter('idCategory', $category); // assigne la valeur de la variable $category au paramètre :idCategory

        //La requête comprend des conditions "optionnelles" :
        if (!empty($type)){ // Si le type est spécifié, 
            $result = $result
                ->andWhere('p.type = :type') //filtre les produits par type 
                ->setParameter('type', $type); // assigne la valeur de la variable $type au paramètre :type
        }

        if(!empty($color)){ // Si la couleur est spécifiée, 
            $result = $result
                ->andWhere('p.productColor = :productColor') //filtre les produits par couleur
                ->setParameter('productColor', $color); // assigne la valeur de la variable $color au paramètre :productColor
        }

        if(!empty($name)){ // Si la dénomination est spécifiée,
            $result = $result
                ->andWhere('p.productName LIKE :productName') //filtre les produits par nom
                ->setParameter('productName', '%' . $name . '%'); // assigne la valeur de la variable $name au paramètre :productName
        }

        if(isset($isPermanent) ){ // Si la gamme est spécifiée,
            $result = $result
                ->andWhere('p.isPermanent = :isPermanent') //filtre les produits par gamme
                ->setParameter('isPermanent', $isPermanent); // assigne la valeur de la variable $isPermanent au paramètre :isPermanent
        }

        $result = $result
            ->orderBy('p.productName','ASC') // trie les résultat par nom dans l'ordre croissant (alphabétique)
            ->getQuery() // crée la requête DQL 
            ->getResult(); // exécute la requête et récupère les résultats (sous forme d'un tableau d'objets)
        
        return $result; // retourne le tableau de résultat 
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

        $year = $dateOfPlacement->format('Y');  //extrait l'année de $dateOfPlacement
        $month = $dateOfPlacement->format('m'); //extrait le mois de $dateOfPlacement
        $startDate = new \DateTime("$year-$month-01 00:00:00"); //définis un objet DataTime représentant le 1er jour du mois 
        $endDate = clone $startDate; //duplication de l'objet préalablement créé; 
        $endDate->modify('last day of this month 23:59:59'); //je modifie l'objet pour qu'il corresponde au dernier mois de l'année
        // "relative Date Modifier" => la chaîne de caractères 'last day of this month' est une instruction

        return $this->createQueryBuilder('p') //création d'un QueryBuilder pour construire une requête DQL
            ->select('p.productName','op.quantity') //récupère les champs productName et quantity de la table Product
            ->innerJoin('p.orderProducts', 'op') //jointure avec l'entité OrderProduct 
            ->innerJoin('op.appOrder', 'o')   //jointure avec l'entité AppOrder
            // jointures effectuées pour ne récupérer que les produits qui ont été intégrés dans une commande
            ->andWhere('o.dateOfPlacement BETWEEN :startDate AND :endDate') //ajout de la condition permettant de sélectionner 
            //les produits faisant partie d'une commande entre ces deux dates
            ->setParameter('startDate', $startDate) // définition valeurs paramètre de la requête 
            ->setParameter('endDate', $endDate) 
            ->orderBy('p.productName', 'ASC') //trie les produits dans l'ordre alphabétique par leur nom
            ->getQuery()
            ->getResult();
    }
}
