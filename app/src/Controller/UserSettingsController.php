<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileImageType;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserSettingsController extends AbstractController
{
    #[Route('/user/settings/profile', name: 'app_user_settings_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(Request $request, UserRepository $userRepository): Response
    {
        /** @type User $user */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();

        $form = $this->createForm(
            UserProfileType::class, $userProfile
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);
            $userRepository->add($user, true);

            $this->addFlash('success', 'User profile has been saved');
            return $this->redirectToRoute('app_user_settings_profile');
        }


        return $this->render('user_settings/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/settings/profile-image', name: 'app_user_settings_profile_image')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(Request $request, SluggerInterface $slugger, UserRepository $userRepository): Response
    {
        /** @type User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserProfileImageType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $profileImageFile = $form->get('profileImage')->getData();

                if ($profileImageFile) {
                    $filename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($filename);
                    $fileExtension = $profileImageFile->guessExtension();
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $fileExtension;

                    $profileImageFile->move($this->getParameter('profiles_directory'), $newFilename);

                    $userProfile = $user->getUserProfile() ?? new UserProfile();
                    $userProfile->setImage($newFilename);
                    $userRepository->add($user, true);

                    $this->addFlash('success', 'Profile image has been updated');
                    return $this->redirectToRoute('app_user_settings_profile_image');
                }
            } catch (FileException | \Exception $exception) {
                throw $exception;
            }
        }

        return $this->render('user_settings/profile_image.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
