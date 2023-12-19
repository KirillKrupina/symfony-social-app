<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile/show/{id}', name: 'app_user_profile_show')]
    public function show(User $user, MicroPostRepository $microPostRepository): Response
    {
        $posts = $microPostRepository->findAllByAuthor($user->getId());

        return $this->render('user_profile/show.html.twig', [
            'user' => $user,
            'posts' => $posts
        ]);
    }

    #[Route('/user/profile/follows/{id}', name: 'app_user_profile_follows')]
    public function follows(User $user): Response
    {
        return $this->render('user_profile/follows.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/user/profile/followers/{id}', name: 'app_user_profile_followers')]
    public function followers(User $user): Response
    {
        return $this->render('user_profile/followers.html.twig', [
            'user' => $user
        ]);
    }
}
