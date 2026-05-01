<?php

namespace App\Infrastructure\Persistence\Doctrine\Photo;

use App\Domain\Photo\Photo;

class PhotoMapper
{
    public static function toDomain(PhotoEntity $entity): Photo
    {
        return new Photo(
            $entity->getId(),
        );
    }

    public static function fromDomain(Photo $photo): PhotoEntity
    {
        $entity = new PhotoEntity($photo->id());
        return $entity;
    }
}