<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Gift;

interface GiftRepositoryInterface
{
    public function findById(string $id): ?Gift;
}
