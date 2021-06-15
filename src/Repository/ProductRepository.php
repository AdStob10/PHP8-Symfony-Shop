<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Products[]        findBestSellingProducts(int $max)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }


    public function findByCategoryId($id, $offset, $max, $order, $filter = "")
    {

        $queryOrder = '';    
        switch($order)
        {
            case 'na':
                $queryOrder = 'p.name ASC';
                break;

            case 'nd':
                $queryOrder = 'p.name DESC';
                break;

            case 'pa':
                $queryOrder = 'p.price ASC';
                break;

            case 'pd':
                $queryOrder = 'p.price DESC';
                break;

            default:
                $queryOrder = 'p.name ASC';
                break;

        }

        if(strlen(trim($filter) > 0))
        {
            $dql = "SELECT p FROM App\Entity\Product p JOIN p.category c WHERE c.id = :id and p.name LIKE :name ORDER BY ".$queryOrder;

            $em = $this->getEntityManager();
            $query = $em->createQuery($dql)
                            ->setParameter(":id", $id)
                            ->setParameter(':name','%'.$filter.'%')
                            ->setFirstResult($offset)
                            ->setMaxResults($max);        
        }
        else
        {
            $dql = "SELECT p FROM App\Entity\Product p JOIN p.category c WHERE c.id = :id ORDER BY ".$queryOrder;

            $em = $this->getEntityManager();
            $query = $em->createQuery($dql)
                            ->setParameter(":id", $id)
                            ->setFirstResult($offset)
                            ->setMaxResults($max);                
        }

        $paginator = new Paginator($query, fetchJoinCollection: false);
        return $paginator;
    }

    public function findBestsellingProducts(int $max)
    {
        $em = $this->getEntityManager();
        return $em->createQuery(
        'SELECT p, sum(op.Quantity * op.priceProduct) as HIDDEN quant 
        FROM App\Entity\Product p 
        JOIN App\Entity\OrderProduct op WITH op.Product = p 
        JOIN App\Entity\Order o WITH op.orderObj = o
        WHERE o.status > 0 
        GROUP BY p.id ORDER BY quant DESC')
                   ->setMaxResults($max)
                   ->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
