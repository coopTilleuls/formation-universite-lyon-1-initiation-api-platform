<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Gift;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class ApiWebTestCase extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = false;

    protected Client $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function getToken(string $username, array $roles = ['ROLE_USER']): string
    {
        $tokenManager = static::getContainer()->get(JWTTokenManagerInterface::class);

        return $tokenManager->create(new InMemoryUser($username, null, $roles));
    }

    protected function createGiftInDatabase(string $name, float $price, string $authorEmail, bool $isPrivate = false): Gift
    {
        $gift = new Gift();
        $gift->setName($name)->setPrice($price)->setAuthorEmail($authorEmail)->setIsPrivate($isPrivate);
        $this->em->persist($gift);
        $this->em->flush();
        $this->em->clear();

        return $gift;
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function post(string $url, array $data, ?string $token = null): ResponseInterface
    {
        $headers = ['Content-Type' => 'application/ld+json'];
        if ($token !== null) {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $this->client->request('POST', $url, [
            'body' => json_encode($data),
            'headers' => $headers,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function get(string $url, ?string $token = null): ResponseInterface
    {
        $headers = $token !== null ? ['Authorization' => 'Bearer '.$token] : [];

        return $this->client->request('GET', $url, ['headers' => $headers]);
    }
}
