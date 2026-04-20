<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;

#[ApiResource(
    types: ['https://schema.org/PostalAddress'],
    provider: [PostalAddress::class, 'getCollection'],
)]
class PostalAddress
{
    public function __construct(
        public readonly ?int $id = null,
        #[ApiProperty(types: ['https://schema.org/addressCountry'])]
        public readonly ?string $addressCountry = null,
        #[ApiProperty(types: ['https://schema.org/postalCode'])]
        public readonly ?string $postalCode = null,
        #[ApiProperty(types: ['https://schema.org/streetAddress'])]
        public readonly ?string $streetAddress = null
    ) {}

    public static function getCollection(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        yield new self(1, 'FR', '44000', '46 rue de Strasbourg');
    }
}