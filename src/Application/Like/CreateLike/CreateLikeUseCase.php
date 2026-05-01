<?php

declare(strict_types=1);

namespace App\Application\Like\CreateLike;

use App\Application\Like\CreateLike\Exception\AlreadyLikedException;
use App\Application\Like\CreateLike\Exception\PhotoNotFoundException;
use App\Application\Like\CreateLike\Exception\UserNotFoundException;
use App\Domain\Like\LikeRepositoryInterface;
use App\Domain\Photo\PhotoRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;

class CreateLikeUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly LikeRepositoryInterface $likeRepository,
        private readonly PhotoRepositoryInterface $photoRepository,
    ) {}

    public function execute(CreateLikeCommand $command): void
    {
        $user = $this->userRepository->getById($command->userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $photo = $this->photoRepository->getById($command->photoId);

        if (!$photo)
        {
            throw new PhotoNotFoundException();
        }

        if ($this->likeRepository->hasLike($photo, $user))
        {
            throw new AlreadyLikedException();
        }

        // transaction required
        $this->likeRepository->createLike($photo, $user);
        $this->photoRepository->increaseLikeCounter($photo);
    }
}
