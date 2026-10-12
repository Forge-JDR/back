<?php
namespace App\Security\Voter;

use App\Entity\Wiki;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WikiElementVoter extends Voter
{
    public const EDIT = 'WIKI_ELEMENT_EDIT';
    public const VIEW = 'WIKI_ELEMENT_VIEW';
    public const DELETE = 'WIKI_ELEMENT_DELETE';

    public function __construct(private Security $security) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE], true)
            && is_object($subject) && method_exists($subject, 'getWiki') && $subject->getWiki() instanceof Wiki;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->security->isGranted($attribute === self::VIEW ? WikiVoter::VIEW : WikiVoter::EDIT, $subject->getWiki());
    }
}
