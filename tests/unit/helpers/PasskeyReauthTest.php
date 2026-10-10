<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\PasskeyReauth;

class PasskeyReauthTest extends Unit
{
    private const NOW = 1_800_000_000;

    public function testBuildStateRoundTrip(): void
    {
        $state = PasskeyReauth::buildState(42, self::NOW);
        $this->assertTrue(PasskeyReauth::isVerified($state, 42, self::NOW));
    }

    public function testValidWithinTtl(): void
    {
        $state = PasskeyReauth::buildState(42, self::NOW);
        $this->assertTrue(
            PasskeyReauth::isVerified($state, 42, self::NOW + PasskeyReauth::TTL),
        );
    }

    public function testExpiredAfterTtl(): void
    {
        $state = PasskeyReauth::buildState(42, self::NOW);
        $this->assertFalse(
            PasskeyReauth::isVerified($state, 42, self::NOW + PasskeyReauth::TTL + 1),
        );
    }

    public function testRejectsFutureTimestamp(): void
    {
        $state = PasskeyReauth::buildState(42, self::NOW + 1);
        $this->assertFalse(PasskeyReauth::isVerified($state, 42, self::NOW));
    }

    public function testRejectsAnotherUser(): void
    {
        $state = PasskeyReauth::buildState(42, self::NOW);
        $this->assertFalse(PasskeyReauth::isVerified($state, 43, self::NOW));
    }

    /**
     * @dataProvider malformedStateProvider
     */
    public function testRejectsMalformedState(mixed $state): void
    {
        $this->assertFalse(PasskeyReauth::isVerified($state, 42, self::NOW));
    }

    public static function malformedStateProvider(): array
    {
        return [
            'null' => [null],
            'string' => ['42'],
            'empty array' => [[]],
            'missing at' => [['user_id' => 42]],
            'missing user_id' => [['at' => self::NOW]],
            'string user_id' => [['user_id' => '42', 'at' => self::NOW]],
            'string at' => [['user_id' => 42, 'at' => (string)self::NOW]],
        ];
    }
}
