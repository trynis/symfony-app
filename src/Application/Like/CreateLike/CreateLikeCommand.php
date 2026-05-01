<?php

namespace App\Application\Like\CreateLike;

final class CreateLikeCommand
{
    public function __construct(
        public int $userId,
        public int $photoId,
    ) {
    }
}