<?php

namespace App\Repository;

use App\Entity\Scenari;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Scenari>
 */
class ScenariRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scenari::class);
    }

    /**
     * Adds a new scenari to the repository.
     *
     * @param Scenari $scenari The Scenari to add
     * @return void
     */
    public function addScenari(Scenari $scenari): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($scenari);
        $entityManager->flush();
    }

    /**
     * Returns all scenari.
     *
     * @return Scenari[] Returns an array of Scenari objects
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('w')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Delete a scenari
     * 
     * @return void Return http code
     */
    public function deleteScenari(Scenari $scenari): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($scenari);
    }

    public function getId() {
        return $this->id;
    }


//    /**
//     * @return Scenari[] Returns an array of Scenari objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Scenari
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
