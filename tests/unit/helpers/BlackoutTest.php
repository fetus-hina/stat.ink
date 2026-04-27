<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\Blackout;
use app\models\User;

use function array_values;

class BlackoutTest extends Unit
{
    public function testFalsyMyPositionReturnsEmpty(): void
    {
        $this->assertSame([], Blackout::getBlackoutTargetList(
            'standard',
            User::BLACKOUT_ALWAYS,
            false,
        ));
    }

    public function testNotBlackoutNeverBlacksOutAnyone(): void
    {
        $this->assertSame([], Blackout::getBlackoutTargetList(
            'standard',
            User::BLACKOUT_NOT_BLACKOUT,
            1,
        ));
    }

    public function testNotPrivateSkipsPrivateLobby(): void
    {
        $this->assertSame([], Blackout::getBlackoutTargetList(
            'private',
            User::BLACKOUT_NOT_PRIVATE,
            1,
        ));
    }

    public function testNotPrivateBlacksOutEveryoneElseInPublicLobby(): void
    {
        $result = array_values(Blackout::getBlackoutTargetList(
            'standard',
            User::BLACKOUT_NOT_PRIVATE,
            3,
        ));
        $this->assertSame([1, 2, 4, 5, 6, 7, 8], $result);
    }

    public function testNotFriendKeepsAlphaTeamWhenSelfIsAlpha(): void
    {
        // squad_4 in position 2 -> alpha team is 1..4, blackout 5..8.
        $result = array_values(Blackout::getBlackoutTargetList(
            'squad_4',
            User::BLACKOUT_NOT_FRIEND,
            2,
        ));
        $this->assertSame([5, 6, 7, 8], $result);
    }

    public function testNotFriendKeepsBravoTeamWhenSelfIsBravo(): void
    {
        $result = array_values(Blackout::getBlackoutTargetList(
            'squad_3',
            User::BLACKOUT_NOT_FRIEND,
            6,
        ));
        $this->assertSame([1, 2, 3, 4], $result);
    }

    public function testNotFriendInPrivateLobbyShowsAll(): void
    {
        $this->assertSame([], Blackout::getBlackoutTargetList(
            'private',
            User::BLACKOUT_NOT_FRIEND,
            5,
        ));
    }

    public function testNotFriendInNonSquadLobbyHidesEveryoneElse(): void
    {
        $result = array_values(Blackout::getBlackoutTargetList(
            'standard',
            User::BLACKOUT_NOT_FRIEND,
            2,
        ));
        $this->assertSame([1, 3, 4, 5, 6, 7, 8], $result);
    }

    public function testAlwaysBlacksOutEveryoneElse(): void
    {
        $result = array_values(Blackout::getBlackoutTargetList(
            'standard',
            User::BLACKOUT_ALWAYS,
            4,
        ));
        $this->assertSame([1, 2, 3, 5, 6, 7, 8], $result);
    }
}
