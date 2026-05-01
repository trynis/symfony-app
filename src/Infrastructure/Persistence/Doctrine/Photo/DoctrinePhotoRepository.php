<?php

declare(strict_types=1);


namespace App\Infrastructure\Persistence\Doctrine\Photo;

use App\Domain\Photo\Photo;
use App\Domain\Photo\PhotoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrinePhotoRepository extends ServiceEntityRepository implements PhotoRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, \App\Infrastructure\Persistence\Doctrine\Photo\PhotoEntity::class);
    }

    public function increaseLikeCounter(Photo $photo): void
    {
        $photoEntity = $this->getEntityManager()->find(PhotoEntity::class, $photo->id());
        $photoEntity->setLikeCounter($photoEntity->getLikeCounter() + 1);
        $this->getEntityManager()->persist($photoEntity);
        $this->getEntityManager()->flush();
    }

    public function decreaseLikeCounter(Photo $photo): void
    {
        $photoEntity = $this->getEntityManager()->find(PhotoEntity::class, $photo->id());
        $photoEntity->setLikeCounter($photoEntity->getLikeCounter() - 1);
        $this->getEntityManager()->persist($photoEntity);
        $this->getEntityManager()->flush();
    }

    public function findAllWithUsers(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getById(int $id): ?Photo
    {
        $entity = $this->find($id);

        if ($entity)
        {
            return PhotoMapper::toDomain($entity);
        }

        return null;
    }
}
