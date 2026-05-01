<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Like;

use App\Domain\Like\Like;
use App\Domain\Like\LikeRepositoryInterface;
use App\Domain\Photo\Photo;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Doctrine\Photo\PhotoEntity;
use App\Infrastructure\Persistence\Doctrine\Photo\PhotoMapper;
use App\Infrastructure\Persistence\Doctrine\User\UserEntity;
use App\Infrastructure\Persistence\Doctrine\User\UserMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineLikeRepository extends ServiceEntityRepository
{
    private ?UserEntity $user;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeEntity::class);
    }

    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
    }

    public function getLike(Photo $photo, User $user): ?Like
    {
        $like = $this->getEntityManager()->createQueryBuilder()
            ->select('l')
            ->from(LikeEntity::class, 'l')
            ->where('l.user = :user')
            ->andWhere('l.photo = :photo')
            ->setParameter('user', UserMapper::fromDomain($user))
            ->setParameter('photo', PhotoMapper::fromDomain($photo))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $like ? LikeMapper::toDomain($like) : null;
    }

    public function removeLike(Like $like): void
    {
        $this->getEntityManager()->remove(LikeMapper::fromDomain($like));
        $this->getEntityManager()->flush();
    }

    public function getLikes(Photo $photo, User $user): array
    {
        $entities = $this->createQueryBuilder('l')
            ->select('l.id')
            ->where('l.user = :user')
            ->andWhere('l.photo = :photo')
            ->setParameter('user', $this->user)
            ->setParameter('photo', $photo)
            ->getQuery()
            ->getArrayResult();

        return array_map(
            fn(LikeEntity $like) => LikeMapper::toDomain($like),
            $entities
        );
    }

    #[\Override]
    public function hasUserLikedPhoto(PhotoEntity $photo): bool
    {
        $likes = $this->createQueryBuilder('l')
            ->select('l.id')
            ->where('l.user = :user')
            ->andWhere('l.photo = :photo')
            ->setParameter('user', $this->user)
            ->setParameter('photo', $photo)
            ->getQuery()
            ->getArrayResult();

        return count($likes) > 0;
    }

    #[\Override]
    public function createLike(Photo $photo, User $user): Like
    {
        $entity = new LikeEntity();
        $entity->setUser(UserMapper::fromDomain($user));
        $entity->setPhoto(PhotoMapper::fromDomain($photo));

        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();

        return LikeMapper::toDomain($entity);
    }

    #[\Override]
    public function updatePhotoCounter(PhotoEntity $photo, int $increment): void
    {
        $em = $this->getEntityManager();
        $photo->setLikeCounter($photo->getLikeCounter() + $increment);
        $em->persist($photo);
        $em->flush();
    }
}
