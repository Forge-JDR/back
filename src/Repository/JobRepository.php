<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\Wiki;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Jobs>
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    public function addJob(Job $job, Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $job->setWiki($wiki);
        $entityManager->persist($job);
        $entityManager->flush();
    }

    public function removeJob(Job $job, Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $wiki->removeJob($job);
        $entityManager->remove($job);
        $entityManager->flush();
    }
//    /**
//     * @return Jobs[] Returns an array of Jobs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Jobs
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
