<?php

namespace App\Domain\Like;

class Like
{
    private int $id;

    public function __construct(
        private readonly int $userId,
        private readonly int $photoId,
    ) {
    }

    public static function create(int $userId, int $photoId): self
    {
        return new self($userId, $photoId);
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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