<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class WikiElementVoter extends Voter
{
    public const EDIT = 'WIKI_ELEMENT_EDIT';
    public const VIEW = 'WIKI_ELEMENT_VIEW';
    public const DELETE = 'WIKI_ELEMENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE]);
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
                    $subject->getWiki()->getUser() === $user ||
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
                    $subject->getWiki()->getUser() === $user ||
                    in_array('ROLE_ADMIN', $user->getRoles()) ||
                    $subject->getWiki()->getStatus() === 'published'
                ) {
                    return true;
                } else {
                    return false;
                }

                break;
                case self::DELETE:
                if (
                    $subject->getWiki()->getUser() === $user ||
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
