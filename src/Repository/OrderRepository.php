<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findUserOrders(User $u, $offset, $max, $order, $status = null)
    {
        
        $status = $status == 0 ? null : $status;
        
        $queryOrder = '';
        switch($order)
        {
            case 'oda':
                $queryOrder='o.createdDate ASC';
                break;
            case 'odd':
                $queryOrder='o.createdDate DESC';
                break;
            case 'sda':
                $queryOrder='o.statusDate ASC';
                break;
            case 'sdd':
                $queryOrder='o.statusDate DESC';
                break;
            default:
                $queryOrder='o.createdDate DESC';
                break;
        }

        $em = $this->getEntityManager();
        if($status == null)
        {
            $query =  $em->createQuery("SELECT o FROM App\Entity\Order o JOIN o.user u WHERE u.id = :id and o.status > :status ORDER BY ".$queryOrder)
                            ->setParameter(":id", $u->getId())
                            ->setParameter(":status", 0)
                            ->setFirstResult($offset)
                            ->setMaxResults($max);
        }
        else
        {
            $query =  $em->createQuery("SELECT o FROM App\Entity\Order o JOIN o.user u WHERE u.id = :id and o.status = :status ORDER BY ".$queryOrder)
                            ->setParameter(":id", $u->getId())
                            ->setParameter(":status", $status)
                            ->setFirstResult($offset)
                            ->setMaxResults($max);
        }

        $paginator = new Paginator($query, fetchJoinCollection:false);
        return $paginator;
    }


    public function findUserOrderWithItems(User $u, int $oid)
    {
        $em = $this->getEntityManager();
        return $em->createQuery("SELECT o, i, p FROM App\Entity\Order o JOIN o.user u JOIN o.items i LEFT JOIN i.Product p WHERE o.id = :oid and u.id = :uid and o.status > 0 ORDER BY o.createdDate DESC")
                        ->setParameter(":oid", $oid)
                        ->setParameter(":uid", $u->getId())
                        ->getOneOrNullResult();
    }

    public function findOrderAsWorker(int $oid)
    {
        $em = $this->getEntityManager();
        return $em->createQuery("SELECT o, i, p FROM App\Entity\Order o JOIN o.user u JOIN o.items i LEFT JOIN i.Product p WHERE o.id = :oid and o.status > 0 ORDER BY o.createdDate DESC")
                        ->setParameter(":oid", $oid)
                        ->getOneOrNullResult();
    }



    public function findOrdersToManage($offset, $max, $order, $status = null)
    {
        $status = $status == 0 ? null : $status;

        $queryOrder = '';
        switch($order)
        {
            case 'oda':
                $queryOrder='o.createdDate ASC';
                break;
            case 'odd':
                $queryOrder='o.createdDate DESC';
                break;
            case 'sda':
                $queryOrder='o.statusDate ASC';
                break;
            case 'sdd':
                $queryOrder='o.statusDate DESC';
                break;
            default:
                $queryOrder='o.createdDate DESC';
                break;
        }


        $em = $this->getEntityManager();
        if($status == null)
        {
            $query =  $em->createQuery("SELECT o, u FROM App\Entity\Order o JOIN o.user u WHERE o.status > :status ORDER BY ".$queryOrder)
                        ->setParameter(":status", 0)
                        ->setFirstResult($offset)
                        ->setMaxResults($max);

        }
        else
        {
            $query =  $em->createQuery("SELECT o, u FROM App\Entity\Order o JOIN o.user u WHERE o.status = :status ORDER BY ".$queryOrder)
            ->setParameter(":status", $status)
            ->setFirstResult($offset)
            ->setMaxResults($max);
        }

        $paginator = new Paginator($query, fetchJoinCollection:true);
        return $paginator->getIterator();
    }


    

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
