<?php

declare(strict_types=1);

namespace App\UI\Http\User;

use App\Infrastructure\Persistence\Doctrine\User\PhotoProviderCredentialEntity;
use App\Infrastructure\Persistence\Doctrine\User\UserEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SavePhotoProviderTokenController extends AbstractController
{
    #[Route('/profile/providers/phoenix/token', name: 'profile_phoenix_token_save', methods: ['POST'])]
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('home');
        }

        if (!$this->isCsrfTokenValid('save_phoenix_token', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid form token. Please try again.');

            return $this->redirectToRoute('profile');
        }

        /** @var UserEntity|null $user */
        $user = $em->getRepository(UserEntity::class)->find($userId);
        if (!$user) {
            $session->clear();

            return $this->redirectToRoute('home');
        }

        $token = trim((string) $request->request->get('token', ''));
        if ($token === '') {
            $this->addFlash('error', 'Token cannot be empty.');

            return $this->redirectToRoute('profile');
        }

        /** @var PhotoProviderCredentialEntity|null $credential */
        $credential = $em->getRepository(PhotoProviderCredentialEntity::class)->findOneBy([
            'user' => $user,
            'provider' => 'phoenix',
        ]);

        if (!$credential) {
            $credential = (new PhotoProviderCredentialEntity())
                ->setUser($user)
                ->setProvider('phoenix');
        }

        $credential
            ->setToken($token)
            ->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($credential);
        $em->flush();

        $this->addFlash('success', 'Phoenix token saved.');

        return $this->redirectToRoute('profile');
    }
}
