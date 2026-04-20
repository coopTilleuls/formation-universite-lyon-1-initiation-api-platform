<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\ApiResource\GiftRead;
use App\Security\GiftReadVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;

final class GiftReadVoterTest extends TestCase
{
    private GiftReadVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new GiftReadVoter();
    }

    public function testPublicGiftIsGrantedToAnyUser(): void
    {
        $gift = new GiftRead(authorEmail: 'john_admin', isPrivate: false);

        $result = $this->voter->vote($this->token('jane_admin'), $gift, [GiftReadVoter::VIEW]);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testPrivateGiftIsGrantedToAuthor(): void
    {
        $gift = new GiftRead(authorEmail: 'john_admin', isPrivate: true);

        $result = $this->voter->vote($this->token('john_admin'), $gift, [GiftReadVoter::VIEW]);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testPrivateGiftIsDeniedToNonAuthor(): void
    {
        $gift = new GiftRead(authorEmail: 'john_admin', isPrivate: true);

        $result = $this->voter->vote($this->token('jane_admin'), $gift, [GiftReadVoter::VIEW]);

        self::assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testAbstainsOnUnsupportedAttribute(): void
    {
        $gift = new GiftRead(authorEmail: 'john_admin', isPrivate: false);

        $result = $this->voter->vote($this->token('john_admin'), $gift, ['OTHER_ATTRIBUTE']);

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testAbstainsOnUnsupportedSubject(): void
    {
        $result = $this->voter->vote($this->token('john_admin'), new \stdClass(), [GiftReadVoter::VIEW]);

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    private function token(string $username, array $roles = ['ROLE_USER']): TokenInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(new InMemoryUser($username, null, $roles));
        $token->method('getRoleNames')->willReturn($roles);

        return $token;
    }
}
