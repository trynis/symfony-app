<?php

declare(strict_types=1);

namespace App\UI\Http;

use App\Infrastructure\Persistence\Doctrine\Like\DoctrineLikeRepository;
use App\Infrastructure\Persistence\Doctrine\Photo\DoctrinePhotoRepository;
use App\Infrastructure\Persistence\Doctrine\Photo\PhotoEntity;
use App\Infrastructure\Persistence\Doctrine\User\UserEntity;
use App\UI\Http\Home\HomePhotoFilterType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EntityManagerInterface $em, ManagerRegistry $managerRegistry): Response
    {
        $photoRepository = new DoctrinePhotoRepository($managerRegistry);
        $likeRepository = new DoctrineLikeRepository($managerRegistry);

        $filterForm = $this->createForm(HomePhotoFilterType::class);
        $filterForm->handleRequest($request);
        $filterData = (array) $filterForm->getData();

        if (
            ($filterData['takenFrom'] ?? null) instanceof DateTimeImmutable
            && ($filterData['takenTo'] ?? null) instanceof DateTimeImmutable
            && $filterData['takenFrom'] > $filterData['takenTo']
        ) {
            $filterForm->get('takenTo')->addError(new FormError('Taken to date must be greater than or equal to taken from date.'));
            $filterData['takenFrom'] = null;
            $filterData['takenTo'] = null;
        }

        $photos = $photoRepository->findAllWithUsersFiltered($this->normalizeFilterData($filterData));

        $session = $request->getSession();
        $userId = $session->get('user_id');
        $currentUser = null;
        $userLikes = [];

        if ($userId) {
            $currentUser = $em->getRepository(UserEntity::class)->find($userId);

            if ($currentUser) {
                $photoIds = array_map(
                    static fn(PhotoEntity $photo) => $photo->getId(),
                    $photos
                );

                $likedPhotoIds = $likeRepository->findLikedPhotoIdsByUserAndPhotos(
                    $currentUser,
                    $photoIds
                );

                $userLikes = array_fill_keys($likedPhotoIds, true);
            }
        }

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
            'currentUser' => $currentUser,
            'userLikes' => $userLikes,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    private function normalizeFilterData(array $filterData): array
    {
        return [
            'location' => trim((string) ($filterData['location'] ?? '')),
            'camera' => trim((string) ($filterData['camera'] ?? '')),
            'description' => trim((string) ($filterData['description'] ?? '')),
            'username' => trim((string) ($filterData['username'] ?? '')),
            'takenFrom' => $filterData['takenFrom'] ?? null,
            'takenTo' => $filterData['takenTo'] ?? null,
        ];
    }
}
