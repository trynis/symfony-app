<?php

namespace App\Domain\Like;

class Like
{

    public function __construct(
        private readonly int $userId,
        private readonly int $photoId,
        private readonly ?int $id = null,
    ) {
    }

    public static function create(int $userId, int $photoId): self
    {
        return new self($userId, $photoId);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    public function photoId(): int
    {
        return $this->photoId;
    }

}