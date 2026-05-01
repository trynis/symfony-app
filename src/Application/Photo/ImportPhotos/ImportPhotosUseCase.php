<?php

declare(strict_types=1);

namespace App\Application\Photo\ImportPhotos;

use App\Application\Photo\ImportPhotos\Exception\PhoenixTokenNotConfiguredException;
use App\Application\Photo\ImportPhotos\Exception\UserNotFoundException;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Photo\PhotoEntity;
use App\Infrastructure\Persistence\Doctrine\User\PhotoProviderCredentialEntity;
use App\Infrastructure\Persistence\Doctrine\User\UserEntity;
use Doctrine\ORM\EntityManagerInterface;

final class ImportPhotosUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly PhoenixPhotoProviderClient $phoenixPhotoProviderClient,
    ) {
    }

    public function execute(ImportPhotosCommand $command): ImportPhotosResult
    {
        if (!$this->userRepository->getById($command->userId)) {
            throw new UserNotFoundException();
        }

        /** @var UserEntity|null $userEntity */
        $userEntity = $this->entityManager->getRepository(UserEntity::class)->find($command->userId);
        if (!$userEntity) {
            throw new UserNotFoundException();
        }

        $token = $command->tokenOverride;
        if ($token === null || trim($token) === '') {
            /** @var PhotoProviderCredentialEntity|null $credential */
            $credential = $this->entityManager->getRepository(PhotoProviderCredentialEntity::class)->findOneBy([
                'user' => $userEntity,
                'provider' => $command->provider,
            ]);

            $token = $credential?->getToken();
        }

        if ($token === null || trim($token) === '') {
            throw new PhoenixTokenNotConfiguredException();
        }

        $externalPhotos = $this->phoenixPhotoProviderClient->fetchPhotos($token);

        $importedCount = 0;
        $skippedCount = 0;

        foreach ($externalPhotos as $externalPhoto) {
            /** @var PhotoEntity|null $existing */
            $existing = $this->entityManager->getRepository(PhotoEntity::class)->findOneBy([
                'user' => $userEntity,
                'provider' => $command->provider,
                'externalPhotoId' => $externalPhoto->externalId,
            ]);

            if ($existing) {
                ++$skippedCount;
                continue;
            }

            $photo = new PhotoEntity();
            $photo
                ->setUser($userEntity)
                ->setProvider($command->provider)
                ->setExternalPhotoId($externalPhoto->externalId)
                ->setImageUrl($externalPhoto->photoUrl);

            $this->entityManager->persist($photo);
            ++$importedCount;
        }

        $this->entityManager->flush();

        return new ImportPhotosResult($importedCount, $skippedCount);
    }
}
