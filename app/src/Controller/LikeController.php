<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LikeController extends AbstractController
{
    #[Route('/like/add/{id}', name: 'app_like_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(MicroPost $post, MicroPostRepository $postRepository, Request $request): Response
    {
        /** @type User $user */
        $user = $this->getUser();
        $post->addLikedBy($user);
        $postRepository->add($post, true);

        return $this->redirect(
            $request->headers->get('referer')
        );
    }

    #[Route('/like/remove/{id}', name: 'app_like_remove')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(MicroPost $post, MicroPostRepository $postRepository, Request $request): Response
    {
        /** @type User $user */
        $user = $this->getUser();
        $post->removeLikedBy($user);
        $postRepository->add($post, true);

        return $this->redirect(
            $request->headers->get('referer')
        );
    }
}
