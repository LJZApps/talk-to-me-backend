<?php

namespace App\Repository;

use App\Entity\InternalNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InternalNews>
 *
 * @method InternalNews|null find($id, $lockMode = null, $lockVersion = null)
 * @method InternalNews|null findOneBy(array $criteria, array $orderBy = null)
 * @method InternalNews[]    findAll()
 * @method InternalNews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InternalNewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InternalNews::class);
    }

    public function getAllNews(): array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return InternalNews[] Returns an array of InternalNews objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InternalNews
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
