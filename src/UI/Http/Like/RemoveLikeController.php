<?php

declare(strict_types=1);

namespace App\UI\Http\Like;

use App\Application\Like\RemoveLike\RemoveLikeCommand;
use App\Application\Like\RemoveLike\RemoveLikeUseCase;
use App\Application\Like\RemoveLike\Exception\LikeNotFoundException;
use App\Application\Like\RemoveLike\Exception\PhotoNotFoundException;
use App\Application\Like\RemoveLike\Exception\UserNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RemoveLikeController extends AbstractController
{
    public function __construct(
        private readonly RemoveLikeUseCase $removeLikeUseCase,
    ) {
    }

    #[Route('/photo/{id}/unlike', name: 'photo_unlike')]
    public function __invoke($id, Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');

        if (!$userId) {
            return new JsonResponse([
                'success' => false,
                'message' => 'You must be logged in',
            ], 401);
        }

        try
        {
            $this->removeLikeUseCase->execute(
                new RemoveLikeCommand(
                    (int) $userId,
                    (int) $id,
                )
            );

            return new JsonResponse([
                'success' => true,
            ]);
        }
        catch (UserNotFoundException $e)
        {
            return new JsonResponse([
                'success' => false,
                'message' => 'User not found',
            ], 404);

        }
        catch (PhotoNotFoundException $e)
        {
            return new JsonResponse([
                'success' => false,
                'message' => 'Photo not found',
            ], 404);

        }
        catch (LikeNotFoundException $e)
        {
            return new JsonResponse([
                'success' => false,
                'message' => 'Like not found',
            ], 409);
        }
    }
}
