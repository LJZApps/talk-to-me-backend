<?php

namespace App\Repository;

use App\Entity\UserStatusPolicy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserStatusPolicy>
 *
 * @method UserStatusPolicy|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserStatusPolicy|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserStatusPolicy[]    findAll()
 * @method UserStatusPolicy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserStatusPolicyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserStatusPolicy::class);
    }

    public function save(UserStatusPolicy $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserStatusPolicy $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPolicyById(int $id): UserStatusPolicy
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.id = :p_id')
            ->setParameter('p_id', $id)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return UserStatusPolicy[] Returns an array of UserStatusPolicy objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserStatusPolicy
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
