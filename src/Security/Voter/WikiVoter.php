<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Wiki;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\RequestStack;

class WikiVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const DELETE = 'DELETE';
    private $requestStack;
    
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    protected function supports(string $attribute, mixed $subject): bool
{
    
    return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
        && $subject instanceof \App\Entity\Wiki;
}

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // If the user is not authenticated, deny access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Ensure the subject is a Wiki entity
        if (!$subject instanceof Wiki) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                // Check if the user is the owner or has an admin role
                if ($subject->getUser() === $user || in_array('ROLE_ADMIN', $user->getRoles())) {
                    // Allow full editing if the status is 'inProgress'
                    if ($subject->getStatus() === 'inProgress') {
                        return true;
                    }

                    // Allow editing only the status field for other statuses
                    $request = $this->requestStack->getCurrentRequest();
                    if ($request && $this->isStatusUpdateRequest($request)) {
                        return true;
                    }
                }
                break;

            case self::VIEW:
                if (
                    $subject->getUser() === $user ||
                    in_array('ROLE_ADMIN', $user->getRoles()) ||
                    $subject->getStatus() === 'published'
                ) {
                    return true;
                }
                break;

            case self::DELETE:
                if ($subject->getUser() === $user || in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * Check if the current request is trying to update only the status.
     */
    private function isStatusUpdateRequest($request): bool
    {
        $content = json_decode($request->getContent(), true);

        return isset($content['Status']) && count($content) === 1;
    }
}
