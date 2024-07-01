<?php

namespace App\Repository;

use App\Entity\Race;
use App\Entity\Wiki;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Races>
 */
class RaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Race::class);
    }

    public function addRace(race $race, Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $race->setWiki($wiki);
        $entityManager->persist($race);
        $entityManager->flush();
    }

    public function removeRace(race $race, Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $wiki->removerace($race);
        $entityManager->remove($race);
        $entityManager->flush();
    }

    //    /**
    //     * @return Races[] Returns an array of Races objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Races
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
