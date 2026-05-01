<?php

declare(strict_types=1);

namespace App\Application\Photo\ImportPhotos;

final class ExternalPhoto
{
    public function __construct(
        public int $externalId,
        public string $photoUrl,
    ) {
    }
}
