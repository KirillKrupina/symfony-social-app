<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use App\Security\Voter\MicroPostVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $microPostRepository): Response
    {
        $posts = $microPostRepository->findAllWithComments();

        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/micro-post/top-liked', name: 'app_micro_post_top_liked')]
    public function topLiked(MicroPostRepository $microPostRepository): Response
    {
        $posts = $microPostRepository->findAllWithMinimumLikes(1);

        return $this->render('micro_post/top_liked.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/micro-post/follows', name: 'app_micro_post_follows')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follows(MicroPostRepository $microPostRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $authors = $user->getFollows();
        $posts = $microPostRepository->findAllByAuthors($authors);

        return $this->render('micro_post/follows.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPostVoter::VIEW, 'post')]
    public function showOne(MicroPost $post): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: 2)]
    #[IsGranted('ROLE_VERIFIED')]
    public function add(Request $request, MicroPostRepository $microPostRepository): Response
    {
        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @type MicroPost $post
             */
            $post = $form->getData();
            $user = $this->getUser();
            $post->setAuthor($user);

            $microPostRepository->add($post, true);

            $this->addFlash('success', 'Successfully added');
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPostVoter::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request, MicroPostRepository $microPostRepository): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $microPostRepository->add($post, true);

            $this->addFlash('success', 'Successfully updated');
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/edit.html.twig', [
            'data' => [
                'post_id' => $post->getId()
            ],
            'form' => $form
        ]);
    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    #[IsGranted('ROLE_COMMENTATOR')]
    public function addComment(MicroPost $post, Request $request, CommentRepository $commentRepository): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @type Comment $comment
             */
            $comment = $form->getData();
            $comment->setPost($post);
            $user = $this->getUser();
            $comment->setAuthor($user);

            $commentRepository->add($comment, true);

            $this->addFlash('success', 'Comment have been added!');
            return $this->redirectToRoute(
                'app_micro_post_show',
                [
                    'post' => $post->getId()
                ]
            );
        }

        return $this->render('micro_post/comment.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }
}
