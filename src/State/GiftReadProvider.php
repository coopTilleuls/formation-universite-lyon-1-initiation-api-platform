<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\GiftRead;
use App\Repository\GiftRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GiftReadProvider implements ProviderInterface
{
    public function __construct(private GiftRepositoryInterface $repository) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): GiftRead
    {
        $entity = $this->repository->findById($uriVariables['id']);
        if (!$entity) {
            throw new NotFoundHttpException('Gift not found.');
        }

        return new GiftRead(
            id: $entity->getId()?->toString(),
            label: $entity->getName(),
            authorEmail: $entity->getAuthorEmail(),
            isPrivate: $entity->isPrivate(),
        );
    }
}
