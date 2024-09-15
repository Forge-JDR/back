<?php

namespace App\Repository;

use App\Entity\Wiki;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\Picture;
use DateTime;

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
    
    public function setPictureFile(Wiki $wiki, File $file, string $fileName): void
    {
        $downloadedPicture = new Picture();
        $downloadedPicture->setFichierImage($file);
        $downloadedPicture->settitle($file->getFilename());
        $downloadedPicture->setPublicPath('uploads/images');;
        $downloadedPicture->setRealPath($fileName);
        $entityManager = $this->getEntityManager();
        $wiki->setImageFile($downloadedPicture);
        $entityManager->persist($downloadedPicture);
        $entityManager->persist($wiki);
        $entityManager->flush();
    }

    /**
     * Returns all wikis.
     *
     * @return Wiki[] Returns an array of Wiki objects
     */
    public function findAllWithStatus(String $status): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.Status LIKE :Status')
            ->setParameter('Status', $status)
            ->getQuery()
            ->getResult();
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

    /**
     * Returns a wiki by id.
     *
     * @param int $id The wiki id
     * @return Wiki|null Returns a Wiki object
     */
    public function findOneById(int $id): ?Wiki
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function removeWiki(Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($wiki);
        $entityManager->flush();
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

    // Remove picture from wiki
    public function removePicture(Wiki $wiki): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($wiki->getImageFile());
        $entityManager->flush();
    }

}

    

