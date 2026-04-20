<?php

declare(strict_types=1);

namespace App\Tests\State;

use ApiPlatform\Metadata\Get;
use App\ApiResource\GiftRead;
use App\Entity\Gift;
use App\Repository\GiftRepositoryInterface;
use App\State\GiftReadProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

final class GiftReadProviderTest extends TestCase
{
    private function buildGift(Uuid $id, string $name, float $price, string $authorEmail, bool $isPrivate = false): Gift
    {
        $gift = new Gift();
        $gift->setName($name)->setPrice($price)->setAuthorEmail($authorEmail)->setIsPrivate($isPrivate);
        (new \ReflectionProperty(Gift::class, 'id'))->setValue($gift, $id);

        return $gift;
    }

    private function makeOperation(): Get
    {
        return new Get(uriTemplate: '/gifts/{id}');
    }

    public function testProvideReturnsMappedDto(): void
    {
        $id = Uuid::v7();
        $gift = $this->buildGift($id, 'Vélo vintage', 250.0, 'john@test.com', true);

        $repository = $this->createMock(GiftRepositoryInterface::class);
        $repository->expects($this->once())->method('findById')->with((string) $id)->willReturn($gift);

        $result = (new GiftReadProvider($repository))->provide($this->makeOperation(), ['id' => (string) $id]);

        self::assertInstanceOf(GiftRead::class, $result);
        self::assertSame((string) $id, $result->id);
        self::assertSame('Vélo vintage', $result->label);
        self::assertSame('john@test.com', $result->authorEmail);
        self::assertTrue($result->isPrivate);
    }

    public function testProvideThrowsNotFoundWhenGiftDoesNotExist(): void
    {
        $repository = $this->createStub(GiftRepositoryInterface::class);
        $repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundHttpException::class);

        (new GiftReadProvider($repository))->provide($this->makeOperation(), ['id' => (string) Uuid::v7()]);
    }

    public function testProvideCallsRepositoryWithCorrectId(): void
    {
        $id = (string) Uuid::v7();

        $repository = $this->createMock(GiftRepositoryInterface::class);
        $repository->expects($this->once())->method('findById')->with($id)->willReturn(null);

        try {
            (new GiftReadProvider($repository))->provide($this->makeOperation(), ['id' => $id]);
        } catch (NotFoundHttpException) {}
    }
}
