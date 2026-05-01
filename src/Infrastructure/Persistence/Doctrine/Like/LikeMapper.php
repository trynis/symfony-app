<?php

namespace App\Infrastructure\Persistence\Doctrine\Like;

use App\Domain\Like\Like;
use App\Infrastructure\Persistence\Doctrine\Like\LikeEntity;

class LikeMapper
{
    public static function toDomain(LikeEntity $entity): Like
    {
        return new Like(
            $entity->getId(),
        );
    }

    public static function fromDomain(Like $like): LikeEntity
    {
        $entity = new LikeEntity($like->id());
        return $entity;
    }
}