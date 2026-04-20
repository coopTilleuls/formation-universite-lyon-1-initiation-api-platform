<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;

final readonly class SecurityAuthenticatedUserProvider implements AuthenticatedUserProviderInterface
{
    public function __construct(private Security $security) {}

    public function getIdentifier(): string
    {
        return $this->security->getUser()->getUserIdentifier();
    }
}
