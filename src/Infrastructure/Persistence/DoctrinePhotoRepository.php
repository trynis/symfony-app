<?php

declare(strict_types=1);


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrinePhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, \App\Domain\Photo\Photo::class);
    }

    public function findAllWithUsers(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
