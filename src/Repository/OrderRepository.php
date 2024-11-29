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

    public function findAllCompleteQuery(){
        return $this->createQueryBuilder('o')
        ->leftJoin('o.OrderItems', 'oi')
        ->addSelect('oi')
        ->leftJoin('oi.product', 'p')
        ->addSelect('p')
        ->getQuery()
        ->getResult();
    }

    public function getOrdersCountByMonth(): array
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = "
        SELECT 
            DATE_FORMAT(o.created_at, '%Y-%m') AS month, 
            COUNT(DISTINCT o.id) AS ordersCount,
            SUM(oi.quantity * oi.product_price) AS totalRevenue
        FROM `order` o
        LEFT JOIN order_item oi on o.id = oi.order_i_id
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
        GROUP BY month
        ORDER BY month ASC;
    ";

    $stmt = $conn->prepare($sql);
    $resultSet = $stmt->executeQuery();

    return $resultSet->fetchAllAssociative();
}

public function findLastFiveOrders(): array
{
    return $this->createQueryBuilder('o')
        ->orderBy('o.createdAt', 'DESC') 
        ->setMaxResults(5) 
        ->getQuery()
        ->getResult();
}

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
