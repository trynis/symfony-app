<?php

declare(strict_types=1);

namespace App\UI\Http\Like;

use App\Application\Like\CreateLike\CreateLikeCommand;
use App\Application\Like\CreateLike\CreateLikeUseCase;
use App\Application\Like\CreateLike\Exception\AlreadyLikedException;
use App\Application\Like\CreateLike\Exception\PhotoNotFoundException;
use App\Application\Like\CreateLike\Exception\UserNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateLikeController extends AbstractController
{
    public function __construct(
        private readonly CreateLikeUseCase $createLikeUseCase,
    ) {
    }

    #[Route('/photo/{id}/like', name: 'photo_like')]
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
            $this->createLikeUseCase->execute(
                new CreateLikeCommand(
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
        catch (AlreadyLikedException $e)
        {
            return new JsonResponse([
                'success' => false,
                'message' => 'Photo already liked',
            ], 409);
        }
    }
}
