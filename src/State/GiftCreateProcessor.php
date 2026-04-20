<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\GiftCreate;
use App\ApiResource\GiftRead;
use App\Entity\Gift;
use App\Security\AuthenticatedUserProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class GiftCreateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private AuthenticatedUserProviderInterface $userProvider,
    ) {}

    /**
     * @param GiftCreate $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GiftRead
    {
        $gift = new Gift();
        $gift->setName($data->label);
        $gift->setPrice($data->price);
        $gift->setAuthorEmail($this->userProvider->getIdentifier());

        $this->persistProcessor->process($gift, $operation, $uriVariables, $context);

        return new GiftRead(
            id: $gift->getId()?->toString(),
            label: $gift->getName(),
            authorEmail: $gift->getAuthorEmail(),
            isPrivate: $gift->isPrivate(),
        );
    }
}
