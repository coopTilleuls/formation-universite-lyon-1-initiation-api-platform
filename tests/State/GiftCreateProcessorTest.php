<?php

declare(strict_types=1);

namespace App\Tests\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\GiftCreate;
use App\Entity\Gift;
use App\Security\AuthenticatedUserProviderInterface;
use App\State\GiftCreateProcessor;
use PHPUnit\Framework\TestCase;

final class GiftCreateProcessorTest extends TestCase
{
    public function testProcessCreatesEntityWithCorrectData(): void
    {
        $persisted = null;

        $this->makeProcessor($this->processorCapturing($persisted), 'jane_admin')
            ->process(new GiftCreate('Guitare', 300.0), new Post());

        self::assertSame('Guitare', $persisted->getName());
        self::assertSame(300.0, $persisted->getPrice());
        self::assertSame('jane_admin', $persisted->getAuthorEmail());
    }

    public function testProcessDelegatesToPersistProcessor(): void
    {
        $persistProcessor = $this->createMock(ProcessorInterface::class);
        $persistProcessor->expects($this->once())
            ->method('process')
            ->with($this->isInstanceOf(Gift::class));

        $this->makeProcessor($persistProcessor, 'john_admin')->process(new GiftCreate('Casque', 89.0), new Post());
    }

    public function testProcessReturnsDtoMatchingCreatedGift(): void
    {
        $result = $this->makeProcessor($this->createStub(ProcessorInterface::class), 'john_admin')
            ->process(new GiftCreate('Livre PHP', 39.99), new Post());

        self::assertSame('Livre PHP', $result->label);
        self::assertSame('john_admin', $result->authorEmail);
        self::assertFalse($result->isPrivate);
    }

    private function makeProcessor(ProcessorInterface $persistProcessor, string $username): GiftCreateProcessor
    {
        $userProvider = $this->createStub(AuthenticatedUserProviderInterface::class);
        $userProvider->method('getIdentifier')->willReturn($username);

        return new GiftCreateProcessor($persistProcessor, $userProvider);
    }

    private function processorCapturing(?Gift &$captured): ProcessorInterface
    {
        $processor = $this->createStub(ProcessorInterface::class);
        $processor->method('process')->willReturnCallback(function (Gift $gift) use (&$captured) {
            $captured = $gift;
        });

        return $processor;
    }
}
