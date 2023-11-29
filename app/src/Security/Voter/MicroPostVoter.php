<?php

namespace App\Security\Voter;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MicroPostVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    /**
     * MicroPostVoter constructor.
     * @param Security $security
     */
    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof MicroPost;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var User $user */
        $user = $token->getUser();

        /** @var MicroPost $microPost */
        $microPost = $subject;

        return match($attribute) {
            self::VIEW => $this->canView(),
            self::EDIT => $this->canEdit($microPost, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(): bool
    {
        return true;
    }

    private function canEdit(MicroPost $microPost, User $user): bool
    {
        $isAuth = $user instanceof UserInterface;
        $isAuthor = $user->getId() === $microPost->getAuthor()->getId();
        $isEditor = $this->security->isGranted('ROLE_EDITOR');

        return $isAuth && ($isAuthor || $isEditor);
    }
}
