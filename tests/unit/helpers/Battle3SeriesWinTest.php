<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\Battle3SeriesWin;
use yii\db\Query;

final class Battle3SeriesWinTest extends Unit
{
    public function testDefaultConditionCoversBothLobbies(): void
    {
        $sql = self::buildSql(Battle3SeriesWin::condition());

        $this->assertStringContainsString("'bankara_challenge'", $sql);
        $this->assertStringContainsString("'xmatch'", $sql);
    }

    public function testSingleLobbyCondition(): void
    {
        $sql = self::buildSql(Battle3SeriesWin::condition([Battle3SeriesWin::LOBBY_XMATCH]));

        $this->assertStringContainsString("'xmatch'", $sql);
        $this->assertStringNotContainsString("'bankara_challenge'", $sql);
    }

    public function testEmptyLobbiesMatchNothing(): void
    {
        $total = (int)new Query()->from('{{%battle3}}')->count();
        if ($total < 1) {
            $this->markTestSkipped('No battles in the database');
        }

        $this->assertSame(
            0,
            (int)new Query()
                ->from('{{%battle3}}')
                ->andWhere(Battle3SeriesWin::condition([]))
                ->count(),
        );
    }

    /**
     * @param array<mixed> $condition
     */
    private static function buildSql(array $condition): string
    {
        return new Query()
            ->from('{{%battle3}}')
            ->andWhere($condition)
            ->createCommand()
            ->rawSql;
    }
}
