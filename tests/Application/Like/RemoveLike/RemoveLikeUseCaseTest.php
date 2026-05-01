<?php

declare(strict_types=1);

namespace App\Tests\Application\Like\RemoveLike;

use App\Application\Like\RemoveLike\Exception\LikeNotFoundException;
use App\Application\Like\RemoveLike\Exception\PhotoNotFoundException;
use App\Application\Like\RemoveLike\Exception\UserNotFoundException;
use App\Application\Like\RemoveLike\RemoveLikeCommand;
use App\Application\Like\RemoveLike\RemoveLikeUseCase;
use App\Domain\Like\LikeRepositoryInterface;
use App\Domain\Photo\Photo;
use App\Domain\Photo\PhotoRepositoryInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RemoveLikeUseCaseTest extends TestCase
{
    private UserRepositoryInterface&MockObject $userRepository;
    private LikeRepositoryInterface&MockObject $likeRepository;
    private PhotoRepositoryInterface&MockObject $photoRepository;
    private RemoveLikeUseCase $useCase;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->likeRepository = $this->createMock(LikeRepositoryInterface::class);
        $this->photoRepository = $this->createMock(PhotoRepositoryInterface::class);

        $this->useCase = new RemoveLikeUseCase(
            $this->userRepository,
            $this->likeRepository,
            $this->photoRepository,
        );
    }

    public function testThrowsWhenUserDoesNotExist(): void
    {
        $command = new RemoveLikeCommand(10, 20);

        $this->userRepository
            ->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn(null);

        $this->photoRepository->expects($this->never())->method('getById');
        $this->likeRepository->expects($this->never())->method('hasLike');
        $this->likeRepository->expects($this->never())->method('removeLike');
        $this->photoRepository->expects($this->never())->method('decreaseLikeCounter');

        $this->expectException(UserNotFoundException::class);
        $this->useCase->execute($command);
    }

    public function testThrowsWhenPhotoDoesNotExist(): void
    {
        $command = new RemoveLikeCommand(10, 20);
        $user = User::create(10);

        $this->userRepository
            ->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($user);

        $this->photoRepository
            ->expects($this->once())
            ->method('getById')
            ->with(20)
            ->willReturn(null);

        $this->likeRepository->expects($this->never())->method('hasLike');
        $this->likeRepository->expects($this->never())->method('removeLike');
        $this->photoRepository->expects($this->never())->method('decreaseLikeCounter');

        $this->expectException(PhotoNotFoundException::class);
        $this->useCase->execute($command);
    }

    public function testThrowsWhenLikeDoesNotExist(): void
    {
        $command = new RemoveLikeCommand(10, 20);
        $user = User::create(10);
        $photo = Photo::create(20);

        $this->userRepository
            ->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($user);

        $this->photoRepository
            ->expects($this->once())
            ->method('getById')
            ->with(20)
            ->willReturn($photo);

        $this->likeRepository
            ->expects($this->once())
            ->method('hasLike')
            ->with($photo, $user)
            ->willReturn(false);

        $this->likeRepository->expects($this->never())->method('removeLike');
        $this->photoRepository->expects($this->never())->method('decreaseLikeCounter');

        $this->expectException(LikeNotFoundException::class);
        $this->useCase->execute($command);
    }

    public function testRemovesLikeAndDecrementsCounterOnHappyPath(): void
    {
        $command = new RemoveLikeCommand(10, 20);
        $user = User::create(10);
        $photo = Photo::create(20);

        $this->userRepository
            ->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($user);

        $this->photoRepository
            ->expects($this->once())
            ->method('getById')
            ->with(20)
            ->willReturn($photo);

        $this->likeRepository
            ->expects($this->once())
            ->method('hasLike')
            ->with($photo, $user)
            ->willReturn(true);

        $this->likeRepository
            ->expects($this->once())
            ->method('removeLike')
            ->with($photo, $user);

        $this->photoRepository
            ->expects($this->once())
            ->method('decreaseLikeCounter')
            ->with($photo);

        $this->useCase->execute($command);
    }
}
