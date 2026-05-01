<?php

declare(strict_types=1);

namespace App\Application\Photo\ImportPhotos;

interface PhoenixPhotoProviderClient
{
    /**
     * @return ExternalPhoto[]
     */
    public function fetchPhotos(string $token): array;
}
