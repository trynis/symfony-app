<?php

declare(strict_types=1);

namespace App\Application\Photo\ImportPhotos;

final class ImportPhotosCommand
{
    public function __construct(
        public int $userId,
        public string $provider,
        public ?string $tokenOverride = null,
    ) {
    }
}
