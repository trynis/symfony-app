<?php

declare(strict_types=1);

namespace App\Application\Like\RemoveLike;

use App\Application\Like\RemoveLike\Exception\LikeNotFoundException;
use App\Application\Like\RemoveLike\Exception\PhotoNotFoundException;
use App\Application\Like\RemoveLike\Exception\UserNotFoundException;
use App\Domain\Like\LikeRepositoryInterface;
use App\Domain\Photo\PhotoRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;

class RemoveLikeUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly LikeRepositoryInterface $likeRepository,
        private readonly PhotoRepositoryInterface $photoRepository,
    ) {}

    public function execute(RemoveLikeCommand $command): void
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

        if (!$this->likeRepository->hasLike($photo, $user))
        {
            throw new LikeNotFoundException();
        }

        // transaction required
        $this->likeRepository->removeLike($photo, $user);
        $this->photoRepository->decreaseLikeCounter($photo);
    }
}
