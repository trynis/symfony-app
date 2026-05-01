<?php

namespace App\Domain\User;

class User
{
    public function __construct(private int $id)
    {
    }

    public static function create(int $id): self
    {
        return new self($id);
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

}