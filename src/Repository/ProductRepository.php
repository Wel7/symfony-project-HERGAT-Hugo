<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
         /**
         * @return Product[] Returns an array of Product objects
         */
        public function queryAllProducts():Query 
        {
            return $this->createQueryBuilder('p')
                ->getQuery()
            ;
        }

        public function queryOneProductForProduct($name) 
        {
            return $this->createQueryBuilder('p')
                ->where('p.name = :name')
                ->setParameter('name', $name)
                ->setMaxResults(1)
                ->getQuery();
        }

        public function getProductAvailabilityCounts(): array
{
    return $this->createQueryBuilder('p')
    ->select('
        SUM(CASE WHEN p.status = :disponible THEN 1 ELSE 0 END) AS disponible,
        SUM(CASE WHEN p.status = :rupture THEN 1 ELSE 0 END) AS rupture,
        SUM(CASE WHEN p.status = :precommande THEN 1 ELSE 0 END) AS precommande
    ')
    ->setParameter('disponible', 'Disponible')
    ->setParameter('rupture', 'Rupture')
    ->setParameter('precommande', 'Precommande')
    ->getQuery()
    ->getSingleResult();
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
