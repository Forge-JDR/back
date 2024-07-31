<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Caracter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CaracterVoter extends Voter
{
    public const EDIT = 'CARACTER_EDIT';
    public const VIEW = 'CARACTER_VIEW';
    public const DELETE = 'CARACTER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\Caracter;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                if (
                    $subject->getUser() === $user ||
                    in_array('ROLE_ADMIN', $user->getRoles())
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                break;

            case self::VIEW:
                if (
                $subject->getUser() === $user ||
                in_array('ROLE_ADMIN', $user->getRoles())
                ) {
                    return true;
                } else {
                    return false;
                }
                break;
            
            case self::DELETE:
                if (
                    $subject->getUser() === $user ||
                    in_array('ROLE_ADMIN', $user->getRoles())
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                break;
        }

        return false;
    }
}
