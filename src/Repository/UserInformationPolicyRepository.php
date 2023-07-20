<?php

namespace App\Repository;

use App\Entity\UserInformationPolicy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserInformationPolicy>
 *
 * @method UserInformationPolicy|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInformationPolicy|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInformationPolicy[]    findAll()
 * @method UserInformationPolicy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInformationPolicyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInformationPolicy::class);
    }

    public function save(UserInformationPolicy $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserInformationPolicy $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UserInformationPolicy[] Returns an array of UserInformationPolicy objects
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

//    public function findOneBySomeField($value): ?UserInformationPolicy
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
