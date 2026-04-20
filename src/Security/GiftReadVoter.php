<?php

declare(strict_types=1);

namespace App\Security;

use App\ApiResource\GiftRead;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

final class GiftReadVoter extends Voter
{
    public const string VIEW = 'GIFT_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof GiftRead;
    }

    /**
     * @param GiftRead $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        if (!$subject->isPrivate) {
            return true;
        }

        return $subject->authorEmail === $token->getUser()?->getUserIdentifier();
    }
}
