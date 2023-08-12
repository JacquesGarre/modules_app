<?php

namespace App\Repository;

use App\Entity\HtmlElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HtmlElement>
 *
 * @method HtmlElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method HtmlElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method HtmlElement[]    findAll()
 * @method HtmlElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HtmlElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HtmlElement::class);
    }

//    /**
//     * @return HtmlElement[] Returns an array of HtmlElement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HtmlElement
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
