<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use App\State\GiftReadProvider;

#[Get(
    uriTemplate: '/gifts/{id}',
    security: "is_granted('GIFT_VIEW', object)",
    provider: GiftReadProvider::class
)]
final readonly class GiftRead
{
    public function __construct(
        public ?string $id = null,
        public ?string $label = null,
        public ?string $authorEmail = null,
        public bool $isPrivate = false,
    ) {}
}
