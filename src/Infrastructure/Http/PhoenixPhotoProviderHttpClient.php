<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Photo\ImportPhotos\Exception\PhotoImportFailedException;
use App\Application\Photo\ImportPhotos\ExternalPhoto;
use App\Application\Photo\ImportPhotos\PhoenixPhotoProviderClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PhoenixPhotoProviderHttpClient implements PhoenixPhotoProviderClient
{
    private const ENDPOINT = 'http://phoenix:4000/api/photos';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function fetchPhotos(string $token): array
    {
        try {
            $response = $this->httpClient->request('GET', self::ENDPOINT, [
                'headers' => [
                    'access-token' => $token,
                ],
            ]);

            if ($response->getStatusCode() >= 400) {
                throw new PhotoImportFailedException('Phoenix provider request failed.');
            }

            $data = $response->toArray(false);
        } catch (TransportExceptionInterface $e) {
            throw new PhotoImportFailedException('Unable to reach Phoenix provider.', previous: $e);
        } catch (\Throwable $e) {
            throw new PhotoImportFailedException('Invalid Phoenix provider response.', previous: $e);
        }

        if (!isset($data['photos']) || !is_array($data['photos'])) {
            throw new PhotoImportFailedException('Phoenix payload does not contain photos list.');
        }

        $photos = [];
        foreach ($data['photos'] as $photo) {
            if (
                !is_array($photo)
                || !isset($photo['id'], $photo['photo_url'])
                || !is_numeric($photo['id'])
                || !is_string($photo['photo_url'])
            ) {
                continue;
            }

            $photos[] = new ExternalPhoto((int) $photo['id'], $photo['photo_url']);
        }

        return $photos;
    }
}
