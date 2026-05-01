<?php

declare(strict_types=1);

namespace App\Application\Photo\ImportPhotos;

final class ImportPhotosResult
{
    public function __construct(
        public int $importedCount,
        public int $skippedCount,
    ) {
    }
}
