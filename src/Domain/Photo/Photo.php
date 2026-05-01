<?php

namespace App\Domain\Photo;

class Photo
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