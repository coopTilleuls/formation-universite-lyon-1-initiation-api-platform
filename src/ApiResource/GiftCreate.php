<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Post;
use App\State\GiftCreateProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[Post(
    uriTemplate: '/gifts',
    output: GiftRead::class,
    processor: GiftCreateProcessor::class
)]
final readonly class GiftCreate
{
    public function __construct(
        #[Assert\NotBlank]
        public string $label,

        #[Assert\Positive]
        public float $price,
    ) {}
}
