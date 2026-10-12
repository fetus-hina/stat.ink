<?php

declare(strict_types=1);

namespace tests\models;

use Codeception\Test\Unit;
use Yii;
use app\models\PasskeyChallenge;
use yii\db\Expression;

use function bin2hex;
use function random_bytes;

/**
 * Uses the database; the Yii2 module rolls back each test
 */
class PasskeyChallengeTest extends Unit
{
    public function testTakeReturnsTheIssuedChallengeOnce(): void
    {
        $issued = PasskeyChallenge::create(
            PasskeyChallenge::PURPOSE_SIGNUP,
            "\x00\x01binary\xff",
            null,
            ['screen_name' => 'foo', 'name' => 'Foo'],
        );

        $taken = PasskeyChallenge::take($issued->token, PasskeyChallenge::PURPOSE_SIGNUP, null);
        $this->assertNotNull($taken);
        $this->assertSame('AAFiaW5hcnn_', $taken->challenge);
        // JSONB does not keep the key order
        $this->assertEquals(['screen_name' => 'foo', 'name' => 'Foo'], $taken->payload);

        $this->assertNull(PasskeyChallenge::take($issued->token, PasskeyChallenge::PURPOSE_SIGNUP, null));
    }

    public function testPayloadIsOptional(): void
    {
        $issued = PasskeyChallenge::create(PasskeyChallenge::PURPOSE_LOGIN, 'challenge', null);

        $taken = PasskeyChallenge::take($issued->token, PasskeyChallenge::PURPOSE_LOGIN, null);
        $this->assertNotNull($taken);
        $this->assertNull($taken->payload);
    }

    public function testCannotTakeWithAnotherPurpose(): void
    {
        $issued = PasskeyChallenge::create(PasskeyChallenge::PURPOSE_LOGIN, 'challenge', null);

        $this->assertNull(PasskeyChallenge::take($issued->token, PasskeyChallenge::PURPOSE_SIGNUP, null));
    }

    public function testBoundToTheUser(): void
    {
        $userId = $this->createUser();
        $issued = PasskeyChallenge::create(PasskeyChallenge::PURPOSE_REAUTH, 'challenge', $userId);

        $this->assertNull(PasskeyChallenge::take($issued->token, PasskeyChallenge::PURPOSE_REAUTH, null));
        $this->assertNull(PasskeyChallenge::take($issued->token, PasskeyChallenge::PURPOSE_REAUTH, $userId + 1));
        $this->assertNotNull(PasskeyChallenge::take($issued->token, PasskeyChallenge::PURPOSE_REAUTH, $userId));
    }

    public function testUnboundChallengeCannotBeTakenAsAUser(): void
    {
        $issued = PasskeyChallenge::create(PasskeyChallenge::PURPOSE_LOGIN, 'challenge', null);

        $this->assertNull(PasskeyChallenge::take($issued->token, PasskeyChallenge::PURPOSE_LOGIN, 1));
    }

    public function testCannotTakeAnExpiredChallenge(): void
    {
        $issued = PasskeyChallenge::create(PasskeyChallenge::PURPOSE_LOGIN, 'challenge', null);
        $this->expire($issued);

        $this->assertNull(PasskeyChallenge::take($issued->token, PasskeyChallenge::PURPOSE_LOGIN, null));
    }

    public function testDeleteExpiredKeepsValidChallenges(): void
    {
        $expired = PasskeyChallenge::create(PasskeyChallenge::PURPOSE_LOGIN, 'challenge', null);
        $this->expire($expired);
        $valid = PasskeyChallenge::create(PasskeyChallenge::PURPOSE_LOGIN, 'challenge', null);

        PasskeyChallenge::deleteExpired();

        $this->assertFalse(PasskeyChallenge::find()->andWhere(['token' => $expired->token])->exists());
        $this->assertTrue(PasskeyChallenge::find()->andWhere(['token' => $valid->token])->exists());
    }

    public function testTokensAreUnique(): void
    {
        $a = PasskeyChallenge::create(PasskeyChallenge::PURPOSE_LOGIN, 'challenge', null);
        $b = PasskeyChallenge::create(PasskeyChallenge::PURPOSE_LOGIN, 'challenge', null);

        $this->assertNotSame($a->token, $b->token);
    }

    private function expire(PasskeyChallenge $model): void
    {
        PasskeyChallenge::updateAll(
            ['expires_at' => new Expression("NOW() - INTERVAL '1 second'")],
            ['token' => $model->token],
        );
    }

    private function createUser(): int
    {
        $suffix = bin2hex(random_bytes(4));
        $db = Yii::$app->db;
        $db->createCommand()
            ->insert('{{%user}}', [
                'name' => 'Test',
                'screen_name' => 'test_' . $suffix,
                'password' => 'dummy',
                'api_key' => 'test-' . bin2hex(random_bytes(16)),
                'join_at' => new Expression('NOW()'),
            ])
            ->execute();
        return (int)$db->getLastInsertID('user_id_seq');
    }
}
