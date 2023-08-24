<?php

namespace App\Repository;

use App\Entity\Layout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Layout>
 *
 * @method Layout|null find($id, $lockMode = null, $lockVersion = null)
 * @method Layout|null findOneBy(array $criteria, array $orderBy = null)
 * @method Layout[]    findAll()
 * @method Layout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LayoutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Layout::class);
    }

//    /**
//     * @return Layout[] Returns an array of Layout objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Layout
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
