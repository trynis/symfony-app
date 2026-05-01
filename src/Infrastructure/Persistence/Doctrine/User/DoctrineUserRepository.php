<?php

declare(strict_types=1);


namespace App\Infrastructure\Persistence\Doctrine\User;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineUserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEntity::class);
    }

    public function getById(int $id): ?User
    {
        $entity = $this->find($id);

        if ($entity)
        {
            return UserMapper::toDomain($entity);
        }

        return null;
    }
}
