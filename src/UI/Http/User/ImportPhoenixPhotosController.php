<?php

declare(strict_types=1);

namespace App\UI\Http\User;

use App\Application\Photo\ImportPhotos\Exception\PhotoImportFailedException;
use App\Application\Photo\ImportPhotos\Exception\PhoenixTokenNotConfiguredException;
use App\Application\Photo\ImportPhotos\Exception\UserNotFoundException;
use App\Application\Photo\ImportPhotos\ImportPhotosCommand;
use App\Application\Photo\ImportPhotos\ImportPhotosUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ImportPhoenixPhotosController extends AbstractController
{
    public function __construct(
        private readonly ImportPhotosUseCase $importPhotosUseCase,
    ) {
    }

    #[Route('/profile/providers/phoenix/import', name: 'profile_phoenix_import', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
        if (!$userId) {
            return $this->redirectToRoute('home');
        }

        if (!$this->isCsrfTokenValid('import_phoenix_photos', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid import request. Please try again.');

            return $this->redirectToRoute('profile');
        }

        try {
            $result = $this->importPhotosUseCase->execute(
                new ImportPhotosCommand((int) $userId, 'phoenix')
            );

            $this->addFlash(
                'success',
                sprintf('Phoenix import finished: %d imported, %d skipped.', $result->importedCount, $result->skippedCount)
            );
        } catch (PhoenixTokenNotConfiguredException $e) {
            $this->addFlash('error', 'Configure your Phoenix token first.');
        } catch (UserNotFoundException $e) {
            $this->addFlash('error', 'User not found.');
        } catch (PhotoImportFailedException $e) {
            $this->addFlash('error', 'Phoenix import failed. Please verify your token and try again.');
        }

        return $this->redirectToRoute('profile');
    }
}
