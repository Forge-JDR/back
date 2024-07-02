<?php

namespace App\Repository;

use App\Entity\Scenario;
use App\Entity\Wiki;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
/**
 * @extends ServiceEntityRepository<Scenarios>
 */
class ScenarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scenario::class);
    }

    public function addScenario(Scenario $Scenario, Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $Scenario->setWiki($wiki);
        $entityManager->persist($Scenario);
        $entityManager->flush();
    }

    public function removeScenario(Scenario $Scenario, Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        if ($Scenario->getWiki() !== $wiki) {
            throw new NotFoundHttpException();
        }
        $wiki->removeScenario($Scenario);
        $entityManager->remove($Scenario);
        $entityManager->flush();
    }

    public function updateScenario(Scenario $Scenario, Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        if ($Scenario->getWiki() !== $wiki) {
            throw new NotFoundHttpException();
        }
        $entityManager->persist($Scenario);
        $entityManager->flush();
    }
//    /**
//     * @return Scenarios[] Returns an array of Scenarios objects
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

//    public function findOneBySomeField($value): ?Scenarios
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
