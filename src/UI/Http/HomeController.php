<?php

declare(strict_types=1);

namespace App\UI\Http;

use App\Domain\User\User;
use App\Infrastructure\Persistence\DoctrineLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use DoctrinePhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return JsonResponse
     */
    public function index(Request $request, EntityManagerInterface $em, ManagerRegistry $managerRegistry): Response
    {
        $photoRepository = new DoctrinePhotoRepository($managerRegistry);
        $likeRepository = new DoctrineLikeRepository($managerRegistry);

        $photos = $photoRepository->findAllWithUsers();

        $session = $request->getSession();
        $userId = $session->get('user_id');
        $currentUser = null;
        $userLikes = [];

        if ($userId) {
            $currentUser = $em->getRepository(User::class)->find($userId);

            if ($currentUser) {
                foreach ($photos as $photo) {
                    $likeRepository->setUser($currentUser);
                    $userLikes[$photo->getId()] = $likeRepository->hasUserLikedPhoto($photo);
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
            'currentUser' => $currentUser,
            'userLikes' => $userLikes,
        ]);
    }
}
