<?php

namespace App\Domain\User;

interface UserRepositoryInterface
{
    public function getById(int $id): ?User;
}