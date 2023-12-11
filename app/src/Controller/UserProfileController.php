<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile/show/{id}', name: 'app_user_profile_show')]
    public function show(User $user): Response
    {
        return $this->render('user_profile/show.html.twig', [
            'user' => $user
        ]);
    }
}
