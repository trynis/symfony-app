<?php

declare(strict_types=1);

namespace App\UI\Http\Like;

use App\Application\Like\CreateLike\CreateLikeUseCase;
use App\Domain\Photo\Photo;
use App\Domain\User\User;
use App\Infrastructure\Persistence\DoctrineLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateLikeController extends AbstractController
{
    #[Route('/photo/{id}/like', name: 'photo_like')]
    public function like($id, Request $request, EntityManagerInterface $em, ManagerRegistry $managerRegistry): Response
    {
        $likeRepository = new DoctrineLikeRepository($managerRegistry);
        $likeService = new CreateLikeUseCase($likeRepository);

        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            $this->addFlash('error', 'You must be logged in to like photos.');
            return $this->redirectToRoute('home');
        }

        $user = $em->getRepository(User::class)->find($userId);
        $photo = $em->getRepository(Photo::class)->find($id);

        $likeRepository->setUser($user);

        if (!$photo) {
            throw $this->createNotFoundException('Photo not found');
        }

        if ($likeRepository->hasUserLikedPhoto($photo)) {
            $likeRepository->unlikePhoto($photo);
            $this->addFlash('info', 'Photo unliked!');
        } else {
            $likeService->execute($photo);
            $this->addFlash('success', 'Photo liked!');
        }

        return $this->redirectToRoute('home');
    }
}
