<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Like;

use App\Infrastructure\Persistence\Doctrine\Photo\PhotoEntity;
use App\Infrastructure\Persistence\Doctrine\User\UserEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineLikeRepository::class)]
#[ORM\Table(name: 'likes')]
class LikeEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private UserEntity $user;

    #[ORM\ManyToOne(targetEntity: PhotoEntity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private PhotoEntity $photo;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): UserEntity
    {
        return $this->user;
    }

    public function setUser(UserEntity $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPhoto(): PhotoEntity
    {
        return $this->photo;
    }

    public function setPhoto(PhotoEntity $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
