<?php

namespace App\Repository;

use App\Entity\Wiki;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wiki>
 */
class WikiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wiki::class);
    }

    
    /**
     * Adds a new wiki to the repository.
     *
     * @param Wiki $wiki The wiki to add
     * @return void
     */
    public function addWiki(Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($wiki);
        $entityManager->flush();
    }
    
    /**
     * Returns all wikis.
     *
     * @return Wiki[] Returns an array of Wiki objects
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('w')
            ->getQuery()
            ->getResult();
    }

    public function deleteWiki(Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($wiki);
    }

    public function updateWiki(Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($wiki);
        $entityManager->flush();
    }

    public function getId() {
        return $this->id;
    }

}

    

