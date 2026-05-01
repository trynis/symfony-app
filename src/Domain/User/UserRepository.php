<?php

namespace App\Domain\User;

use App\Domain\Photo\Photo;
use App\Infrastructure\Persistence\Doctrine\User\DoctrineUserRepository;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly DoctrineUserRepository $doctrineUserRepository
    )
    {
    }

    public function getById(int $id): ?User
    {
        return $this->doctrineUserRepository->getById($id);
    }

}