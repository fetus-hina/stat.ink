<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\db\Now;
use yii\db\Query;

/**
 * Recount the Anarchy Battle (Series) wins, which require the game version 6.0.0 or later
 * like the X Battle wins.
 */
final class m261119_101641_user_badge3_series_win_bankara_version extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->recount(true);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->recount(false);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%user_badge3_series_win}}',
        ];
    }

    private function recount(bool $requireVersion): void
    {
        $lobbyId = $this->key2id('{{%lobby3}}', 'bankara_challenge');

        $this->delete('{{%user_badge3_series_win}}', ['lobby_id' => $lobbyId]);

        $now = new Now();
        $query = new Query()
            ->select([
                'user_id' => '{{%battle3}}.[[user_id]]',
                'lobby_id' => '{{%battle3}}.[[lobby_id]]',
                'count' => 'COUNT(*)',
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->from('{{%battle3}}')
            ->andWhere([
                '{{%battle3}}.[[is_deleted]]' => false,
                '{{%battle3}}.[[lobby_id]]' => $lobbyId,
                '{{%battle3}}.[[result_id]]' => $this->key2id('{{%result3}}', 'win'),
                '{{%battle3}}.[[challenge_win]]' => 5,
            ])
            ->groupBy([
                '{{%battle3}}.[[user_id]]',
                '{{%battle3}}.[[lobby_id]]',
            ]);

        if ($requireVersion) {
            $query->andWhere([
                '{{%battle3}}.[[version_id]]' => new Query()
                    ->select(['id'])
                    ->from('{{%splatoon_version3}}')
                    ->andWhere(['>=', 'release_at', new Query()
                        ->select(['release_at'])
                        ->from('{{%splatoon_version3}}')
                        ->andWhere(['tag' => '6.0.0']),
                    ]),
            ]);
        }

        $this->execute(
            vsprintf('INSERT INTO {{%%user_badge3_series_win}} %s', [
                $query->createCommand()->rawSql,
            ]),
        );
    }
}
