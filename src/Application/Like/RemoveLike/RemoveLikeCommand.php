<?php

namespace App\Application\Like\RemoveLike;

final class RemoveLikeCommand
{
    public function __construct(
        public int $userId,
        public int $photoId,
    ) {
    }
}