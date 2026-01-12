<?php

namespace App\Repository;

use App\Entity\Factory;
use App\Entity\Machine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Machine>
 */
class MachineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Machine::class);
    }

//    /**
//     * @return Machine[] Returns an array of Machine objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Machine
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findOrphanedMachinesForFactory(Factory $factory): array {
        $queryBuilder = $this -> createQueryBuilder('m');
        $expression = $queryBuilder -> expr();

        $excludeQuery = $this -> createQueryBuilder('sub')
        -> select("sub.id")
        -> join("sub.factory", 'f')
        -> where("f = :factory");

        return $queryBuilder
        -> where($expression-> notIn('m.id', $excludeQuery -> getDQL()))
        -> setParameter("factory", $factory)
        -> getQuery()
        -> getResult();
    }
}
