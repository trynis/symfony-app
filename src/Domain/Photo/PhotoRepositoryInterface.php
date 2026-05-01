<?php

namespace App\Domain\Photo;

interface PhotoRepositoryInterface
{
    public function getById(int $id): ?Photo;

    public function increaseLikeCounter(Photo $photo): void;

    public function decreaseLikeCounter(Photo $photo): void;
}