<?php

declare(strict_types=1);

namespace App\Tests\Application\Like\CreateLike;

use App\Application\Like\CreateLike\CreateLikeCommand;
use App\Application\Like\CreateLike\CreateLikeUseCase;
use App\Application\Like\CreateLike\Exception\AlreadyLikedException;
use App\Application\Like\CreateLike\Exception\PhotoNotFoundException;
use App\Application\Like\CreateLike\Exception\UserNotFoundException;
use App\Domain\Like\Like;
use App\Domain\Like\LikeRepositoryInterface;
use App\Domain\Photo\Photo;
use App\Domain\Photo\PhotoRepositoryInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateLikeUseCaseTest extends TestCase
{
    private UserRepositoryInterface&MockObject $userRepository;
    private LikeRepositoryInterface&MockObject $likeRepository;
    private PhotoRepositoryInterface&MockObject $photoRepository;
    private CreateLikeUseCase $useCase;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->likeRepository = $this->createMock(LikeRepositoryInterface::class);
        $this->photoRepository = $this->createMock(PhotoRepositoryInterface::class);

        $this->useCase = new CreateLikeUseCase(
            $this->userRepository,
            $this->likeRepository,
            $this->photoRepository,
        );
    }

    public function testThrowsWhenUserDoesNotExist(): void
    {
        $command = new CreateLikeCommand(10, 20);

        $this->userRepository
            ->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn(null);

        $this->photoRepository->expects($this->never())->method('getById');
        $this->likeRepository->expects($this->never())->method('hasLike');
        $this->likeRepository->expects($this->never())->method('createLike');
        $this->photoRepository->expects($this->never())->method('increaseLikeCounter');

        $this->expectException(UserNotFoundException::class);
        $this->useCase->execute($command);
    }

    public function testThrowsWhenPhotoDoesNotExist(): void
    {
        $command = new CreateLikeCommand(10, 20);
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
        $this->likeRepository->expects($this->never())->method('createLike');
        $this->photoRepository->expects($this->never())->method('increaseLikeCounter');

        $this->expectException(PhotoNotFoundException::class);
        $this->useCase->execute($command);
    }

    public function testThrowsWhenLikeAlreadyExists(): void
    {
        $command = new CreateLikeCommand(10, 20);
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

        $this->likeRepository->expects($this->never())->method('createLike');
        $this->photoRepository->expects($this->never())->method('increaseLikeCounter');

        $this->expectException(AlreadyLikedException::class);
        $this->useCase->execute($command);
    }

    public function testCreatesLikeAndIncrementsCounterOnHappyPath(): void
    {
        $command = new CreateLikeCommand(10, 20);
        $user = User::create(10);
        $photo = Photo::create(20);
        $like = Like::create(10, 20);

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

        $this->likeRepository
            ->expects($this->once())
            ->method('createLike')
            ->with($photo, $user)
            ->willReturn($like);

        $this->photoRepository
            ->expects($this->once())
            ->method('increaseLikeCounter')
            ->with($photo);

        $this->useCase->execute($command);
    }
}
