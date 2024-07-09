<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PictureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTime;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
#[Vich\Uploadable]
class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['picture.index', 'picture.details', 'wiki.details'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['picture.index', 'picture.details', 'wiki.details'])]
    private ?string $title = null;

    #[Vich\UploadableField(mapping: 'imagesload', fileNameProperty: 'realPath')]
    #[Groups(['picture.details'])]
    private ?File $fichierImage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['picture.details'])]
    private ?\DateTimeInterface $updateAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['picture.details'])]
    private ?string $publicPath = null;

    #[ORM\Column(length: 255)]
    #[Groups(['picture.details'])]
    private ?string $realPath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function gettitle(): ?string
    {
        return $this->title;
    }

    public function settitle(?string $title)
    {
        $this->title = $title;

        return $this;
    }

     /**
     * @return null|File
     */
    public function getFichierImage(): ?File
    {
        return $this->fichierImage;
    }

    /**
     * @param File|null $fichierImage
     */
    public function setFichierImage(?File $fichierImage = null)
    {
        $this->fichierImage = $fichierImage;
        if (null !== $fichierImage) {
            $this->updateAt = new DateTime();
        }
        return $this;
    }


    public function getPublicPath(): ?string
    {
        return $this->publicPath;
    }

    public function setPublicPath(?string $publicPath)
    {
        $this->publicPath = $publicPath;

        return $this;
    }

    public function getRealPath(): ?string
    {
        return $this->realPath;
    }

    public function setRealPath(?string $realPath)
    {
        $this->realPath = $realPath;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }
}