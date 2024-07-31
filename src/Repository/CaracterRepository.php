<?php

namespace App\Repository;

use App\Entity\Caracter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\Picture;
use DateTime;

/**
 * @extends ServiceEntityRepository<Caracter>
 */
class CaracterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Caracter::class);
    }

     /**
     * Adds a new Caracter to the repository.
     *
     * @param Caracter $Caracter The Caracter to add
     * @return void
     */
    public function addCaracter(Caracter $Caracter): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($Caracter);
        $entityManager->flush();
    }

    public function setPictureFile(Caracter $Caracter, File $file, string $fileName): void
    {
        $downloadedPicture = new Picture();
        $downloadedPicture->setFichierImage($file);
        $downloadedPicture->settitle($file->getFilename());
        $downloadedPicture->setPublicPath('uploads/images');;
        $downloadedPicture->setRealPath($fileName);
        $entityManager = $this->getEntityManager();
        $Caracter->setImageFile($downloadedPicture);
        $entityManager->persist($downloadedPicture);
        $entityManager->persist($Caracter);
        $entityManager->flush();
    }

    /**
     * Returns all caracters.
     *
     * @return Caracters[] Returns an array of Caracters objects
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('w')
            ->getQuery()
            ->getResult();
    }

    public function removeCaracter(Caracter $caracter): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($caracter);
        $entityManager->flush();
    }

    public function updateCaracter(Caracter $caracter): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($caracter);
        $entityManager->flush();
    }

    // Remove picture from caracter
    public function removePicture(Caracter $caracter): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($caracter->getImageFile());
        $entityManager->flush();
    }
}