<?php

namespace App\Infrastructure\Persistence\Doctrine\User;

use App\Domain\User\User;
use App\Infrastructure\Persistence\Doctrine\User\UserEntity;

class UserMapper
{
    public static function toDomain(UserEntity $entity): User
    {
        return new User(
            $entity->getId(),
        );
    }

    public static function fromDomain(User $user): UserEntity
    {
        $entity = new UserEntity($user->id());
        return $entity;
    }
}