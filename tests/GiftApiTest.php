<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class GiftApiTest extends ApiWebTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testCreateGiftReturns201(): void
    {
        $this->post('/api/gifts', ['label' => 'Vélo vintage', 'price' => 250.0], $this->getToken('john_admin'));

        self::assertResponseStatusCodeSame(201);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateGiftResponseShape(): void
    {
        $response = $this->post('/api/gifts', ['label' => 'Livre PHP', 'price' => 39.99], $this->getToken('john_admin'));

        self::assertResponseStatusCodeSame(201);
        $data = $response->toArray();
        self::assertSame('Livre PHP', $data['label']);
        self::assertSame('john_admin', $data['authorEmail']);
        self::assertArrayNotHasKey('price', $data);
    }

    public function testCreateGiftWithoutAuthReturns401(): void
    {
        $this->post('/api/gifts', ['label' => 'Cadeau', 'price' => 50.0]);

        self::assertResponseStatusCodeSame(401);
    }

    public static function invalidGiftPayloadProvider(): \Generator
    {
        yield 'blank label' => [['label' => '', 'price' => 50.0]];
        yield 'zero price' => [['label' => 'Cadeau', 'price' => 0.0]];
        yield 'negative price' => [['label' => 'Cadeau', 'price' => -10.0]];
    }

    #[DataProvider('invalidGiftPayloadProvider')]
    public function testCreateGiftWithInvalidPayloadReturns422(array $payload): void
    {
        $this->post('/api/gifts', $payload, $this->getToken('john_admin'));

        self::assertResponseStatusCodeSame(422);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateGiftSetsAuthorFromAuthenticatedUser(): void
    {
        $this->post('/api/gifts', ['label' => 'Guitare', 'price' => 300.0], $this->getToken('jane_admin'));

        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains(['authorEmail' => 'jane_admin']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetPublicGiftResponseShape(): void
    {
        $gift = $this->createGiftInDatabase('Appareil photo', 450.0, 'john_admin', false);

        $response = $this->get(sprintf('/api/gifts/%s', $gift->getId()), $this->getToken('jane_admin'));
        $data = $response->toArray();

        self::assertResponseStatusCodeSame(200);
        self::assertSame((string) $gift->getId(), $data['id']);
        self::assertSame('Appareil photo', $data['label']);
        self::assertSame('john_admin', $data['authorEmail']);
        self::assertFalse($data['isPrivate']);
        self::assertArrayNotHasKey('price', $data);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetPrivateGiftAsAuthorReturns200(): void
    {
        $gift = $this->createGiftInDatabase('Cadeau secret', 100.0, 'john_admin', true);

        $this->get(sprintf('/api/gifts/%s', $gift->getId()), $this->getToken('john_admin'));

        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetPrivateGiftAsNonAuthorReturns403(): void
    {
        $gift = $this->createGiftInDatabase('Cadeau secret', 100.0, 'john_admin', true);

        $this->get(sprintf('/api/gifts/%s', $gift->getId()), $this->getToken('jane_admin'));

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetNonExistentGiftReturns404(): void
    {
        $this->get(sprintf('/api/gifts/%s', Uuid::v7()), $this->getToken('john_admin'));

        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetGiftWithoutAuthReturns401(): void
    {
        $gift = $this->createGiftInDatabase('Livre PHP', 35.0, 'john_admin', false);

        $this->get(sprintf('/api/gifts/%s', $gift->getId()));

        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testPostThenGetReturnsCorrectData(): void
    {
        $token = $this->getToken('john_admin');

        $id = $this->post('/api/gifts', ['label' => 'Vélo vintage', 'price' => 250.0], $token)->toArray()['id'];
        self::assertResponseStatusCodeSame(201);

        $this->get(sprintf('/api/gifts/%s', $id), $token);
        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains(['label' => 'Vélo vintage', 'authorEmail' => 'john_admin']);
    }
}
