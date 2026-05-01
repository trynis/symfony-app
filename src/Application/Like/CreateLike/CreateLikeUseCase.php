<?php

declare(strict_types=1);

namespace App\Application\Like\CreateLike;

use App\Domain\Like\LikeRepositoryInterface;
use App\Domain\Photo\Photo;

class CreateLikeUseCase
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository
    ) {}

    public function execute(Photo $photo): void
    {
        try {
            $this->likeRepository->createLike($photo);
            $this->likeRepository->updatePhotoCounter($photo, 1);
        } catch (\Throwable $e) {
            throw new \Exception('Something went wrong while liking the photo');
        }
    }
}
