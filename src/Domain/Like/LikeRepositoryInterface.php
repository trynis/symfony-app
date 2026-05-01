<?php
declare(strict_types=1);

namespace App\Domain\Like;

use App\Domain\Photo\Photo;
use App\Domain\User\User;

interface LikeRepositoryInterface
{
    // better would be createLike(Like $like): Like;
    public function createLike(Photo $photo, User $user): Like;

    public function removeLike(Photo $photo, User $user): void;

    public function hasLike(Photo $photo, User $user): bool;

}