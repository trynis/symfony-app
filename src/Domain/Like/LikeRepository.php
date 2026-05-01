<?php

namespace App\Domain\Like;

use App\Domain\Photo\Photo;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Doctrine\Like\DoctrineLikeRepository;
use App\Infrastructure\Persistence\Doctrine\Like\LikeEntity;
use App\Infrastructure\Persistence\Doctrine\Photo\DoctrinePhotoRepository;
use App\Infrastructure\Persistence\Doctrine\Photo\PhotoEntity;

class LikeRepository implements LikeRepositoryInterface
{
    public function __construct(
        private readonly DoctrineLikeRepository $doctrineLikeRepository
    )
    {
    }

    public function removeLike(Photo $photo, User $user): void
    {
        $like = $this->doctrineLikeRepository->getLike($photo, $user);

        if ($like)
        {
            $this->doctrineLikeRepository->removeLike($like);
        }

        // potential for an exception
    }

    public function hasLike(Photo $photo, User $user): bool
    {
        return count($this->doctrineLikeRepository->getLikes($photo, $user)) > 0;
    }

    public function createLike(Photo $photo, User $user): Like
    {
        return $this->doctrineLikeRepository->createLike($photo, $user);
    }

}