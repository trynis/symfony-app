<?php

namespace App\Domain\User;

class User
{
    private int $id;

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

}