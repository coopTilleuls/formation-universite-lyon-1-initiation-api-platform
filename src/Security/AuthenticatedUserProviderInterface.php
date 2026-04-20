<?php

declare(strict_types=1);

namespace App\Security;

interface AuthenticatedUserProviderInterface
{
    public function getIdentifier(): string;
}
