<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const VIEW = 'USER_VIEW';
    public const DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
{
    $user = $token->getUser();
    
    if (!$user instanceof UserInterface) {
        
        return false;
    }

    switch ($attribute) {
        case self::EDIT:
            if (
                $subject === $user || 
                in_array('ROLE_ADMIN', $user->getRoles())
            ) {
                
                return true;
            }
            break;

        case self::VIEW:
            if (
                $subject === $user || 
                in_array('ROLE_ADMIN', $user->getRoles())
            ) {
                return true;
            }
            break;

        case self::DELETE:
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return true;
            }
            break;
    }

    return false;
}

}
