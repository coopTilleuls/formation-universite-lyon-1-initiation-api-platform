<?php

declare(strict_types=1);

namespace App\Entity;

enum AgentRole: string
{
    case ROLE_PROJECT_CONTRIBUTOR = 'PROJECT_CONTRIBUTOR';
    case ROLE_PROJECT_MANAGER = 'PROJECT_MANAGER';
    case ROLE_ADMIN = 'ADMIN';

    public function isAdmin(): bool
    {
        return $this ===  self::ROLE_ADMIN;
    }
}
