<?php

namespace App\Infrastructure\Persistence\Doctrine\Like;

use App\Domain\Like\Like;
use App\Domain\Photo\Photo;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Doctrine\Like\LikeEntity;
use App\Infrastructure\Persistence\Doctrine\Photo\PhotoMapper;
use App\Infrastructure\Persistence\Doctrine\User\UserMapper;

class LikeMapper
{
    public static function toDomain(LikeEntity $entity): Like
    {
        return new Like(
            $entity->getId(),
            $entity->getUser()->getId(),
            $entity->getPhoto()->getId(),
        );
    }

    public static function fromDomain(Like $like): LikeEntity
    {
        $entity = new LikeEntity($like->id());
        $entity->setPhoto(PhotoMapper::fromDomain(Photo::create($like->photoId())));
        $entity->setUser(UserMapper::fromDomain(User::create($like->userId()))) ;
        return $entity;
    }
}