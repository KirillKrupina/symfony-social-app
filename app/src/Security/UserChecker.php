<?php

namespace App\Security;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        $bannedDate = $user->getBannedUntil();

        if ($bannedDate === null) {
            return;
        }

        $currentDate = new \DateTime();
        if ($currentDate < $bannedDate) {
//            throw new CustomUserMessageAccountStatusException('Your user account is banned until ' . $bannedDate->format('Y-m-d H:i:s'));
            throw new AccessDeniedHttpException('Your user account is banned until ' . $bannedDate->format('Y-m-d H:i:s'));
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {

    }
}