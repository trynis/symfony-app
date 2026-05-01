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

final class DoctrineLikeRepository extends ServiceEntityRepository implements LikeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeEntity::class);
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

    public function removeLike(Photo $photo, User $user): void
    {
        $like = $this->getLike($photo, $user);

        $likeEntity = $this->find($like->id());
        $this->getEntityManager()->remove($likeEntity);
        $this->getEntityManager()->flush();
    }

    public function hasLike(Photo $photo, User $user): bool
    {
        return count($this->getLikes($photo, $user)) > 0;
    }

    public function getLikes(Photo $photo, User $user): array
    {
        $entities = $this->createQueryBuilder('l')
            ->select('l')
            ->where('l.user = :user')
            ->andWhere('l.photo = :photo')
            ->setParameter('user', UserMapper::fromDomain($user))
            ->setParameter('photo', PhotoMapper::fromDomain($photo))
            ->getQuery()
            ->getResult();

        return array_map(
            fn(LikeEntity $like) => LikeMapper::toDomain($like),
            $entities
        );
    }

    public function findLikedPhotoIdsByUserAndPhotos(
        UserEntity $user,
        array $photoIds
    ): array {
        if ($photoIds === []) {
            return [];
        }

        $rows = $this->createQueryBuilder('l')
            ->select('IDENTITY(l.photo) AS photo_id')
            ->where('l.user = :user')
            ->andWhere('l.photo IN (:photoIds)')
            ->setParameter('user', $user)
            ->setParameter('photoIds', $photoIds)
            ->getQuery()
            ->getArrayResult();

        return array_map(
            static fn(array $row) => (int) $row['photo_id'],
            $rows
        );
    }

    public function createLike(Photo $photo, User $user): Like
    {
        $entity = new LikeEntity();
        $userEntity = $this->getEntityManager()->getReference(
            UserEntity::class,
            $user->id(),
        );
        $photoEntity = $this->getEntityManager()->getReference(
            PhotoEntity::class,
            $photo->id(),
        );
        $entity->setUser($userEntity);
        $entity->setPhoto($photoEntity);

        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();

        return LikeMapper::toDomain($entity);
    }

    public function updatePhotoCounter(PhotoEntity $photo, int $increment): void
    {
        $em = $this->getEntityManager();
        $photo->setLikeCounter($photo->getLikeCounter() + $increment);
        $em->persist($photo);
        $em->flush();
    }

    public function getById(int $id): ?Like
    {
        $entity = $this->find($id);

        if ($entity)
        {
            return LikeMapper::toDomain($entity);
        }

        return null;
    }
}
