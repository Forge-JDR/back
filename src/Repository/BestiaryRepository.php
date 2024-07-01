<?php

namespace App\Repository;

use App\Entity\Bestiary;
use App\Entity\Wiki;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bestiaries>
 */
class BestiaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bestiary::class);
    }

    public function addBestiary( bestiary $bestiary, Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $bestiary->setWiki($wiki);
        $entityManager->persist($bestiary);
        $entityManager->flush();
    }

    public function removeBestiary( Bestiary $bestiary, Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $wiki->removeBestiary($bestiary);
        $entityManager->remove($bestiary);
        $entityManager->flush();
    }
    //    /**
    //     * @return Bestiaries[] Returns an array of Bestiaries objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Bestiaries
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
