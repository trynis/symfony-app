<?php

namespace App\Domain\Photo;

use App\Domain\Like\Like;
use App\Domain\Like\LikeRepositoryInterface;
use App\Domain\Photo\Photo;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Doctrine\Like\DoctrineLikeRepository;
use App\Infrastructure\Persistence\Doctrine\Like\LikeEntity;
use App\Infrastructure\Persistence\Doctrine\Photo\DoctrinePhotoRepository;
use App\Infrastructure\Persistence\Doctrine\Photo\PhotoEntity;

class PhotoRepository implements PhotoRepositoryInterface
{
    public function __construct(
        private readonly DoctrinePhotoRepository $doctrinePhotoRepository
    )
    {
    }

    public function getById(int $id): ?Photo
    {
        return $this->doctrinePhotoRepository->getById($id);
    }

    public function increaseLikeCounter(Photo $photo): void
    {
        $this->doctrinePhotoRepository->increaseLikeCounter($photo);
    }

    public function decreaseLikeCounter(Photo $photo): void
    {
        $this->doctrinePhotoRepository->decreaseLikeCounter($photo);
    }
}