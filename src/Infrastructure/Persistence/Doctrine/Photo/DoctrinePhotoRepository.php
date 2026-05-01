<?php

declare(strict_types=1);


namespace App\Infrastructure\Persistence\Doctrine\Photo;

use App\Domain\Photo\Photo;
use App\Domain\Photo\PhotoRepositoryInterface;
use DateTimeImmutable;
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

    public function findAllWithUsersFiltered(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u');

        if (!empty($criteria['location'])) {
            $queryBuilder
                ->andWhere('LOWER(p.location) LIKE :location')
                ->setParameter('location', '%' . strtolower((string) $criteria['location']) . '%');
        }

        if (!empty($criteria['camera'])) {
            $queryBuilder
                ->andWhere('LOWER(p.camera) LIKE :camera')
                ->setParameter('camera', '%' . strtolower((string) $criteria['camera']) . '%');
        }

        if (!empty($criteria['description'])) {
            $queryBuilder
                ->andWhere('LOWER(p.description) LIKE :description')
                ->setParameter('description', '%' . strtolower((string) $criteria['description']) . '%');
        }

        if (!empty($criteria['username'])) {
            $queryBuilder
                ->andWhere('LOWER(u.username) LIKE :username')
                ->setParameter('username', '%' . strtolower((string) $criteria['username']) . '%');
        }

        if (($criteria['takenFrom'] ?? null) instanceof DateTimeImmutable) {
            $queryBuilder
                ->andWhere('p.takenAt >= :takenFrom')
                ->setParameter('takenFrom', $criteria['takenFrom']->setTime(0, 0, 0));
        }

        if (($criteria['takenTo'] ?? null) instanceof DateTimeImmutable) {
            $queryBuilder
                ->andWhere('p.takenAt <= :takenTo')
                ->setParameter('takenTo', $criteria['takenTo']->setTime(23, 59, 59));
        }

        return $queryBuilder
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
