<?php

declare(strict_types=1);

namespace App\Security\Voter;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\String\u;

#[AsTaggedItem(priority: 255)]
final class ProjectPostVoter extends Voter
{
    public const CREATE = 'CREATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::CREATE
            && $subject instanceof \App\Entity\Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // $user->getAgent()->hasRole("ROLE_PROJECT_MANAGER")
        return u($user->getUserIdentifier())->containsAny('john');
    }
}
