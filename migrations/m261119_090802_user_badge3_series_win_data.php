<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\db\Now;
use yii\db\Query;

final class m261119_090802_user_badge3_series_win_data extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $now = new Now();
        $this->execute(
            vsprintf('INSERT INTO {{%%user_badge3_series_win}} %s', [
                new Query()
                    ->select([
                        'user_id' => '{{%battle3}}.[[user_id]]',
                        'lobby_id' => '{{%battle3}}.[[lobby_id]]',
                        'count' => 'COUNT(*)',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                    ->from('{{%battle3}}')
                    ->andWhere(['{{%battle3}}.[[is_deleted]]' => false])
                    ->andWhere($this->seriesWinCondition())
                    ->groupBy([
                        '{{%battle3}}.[[user_id]]',
                        '{{%battle3}}.[[lobby_id]]',
                    ])
                    ->createCommand()
                    ->rawSql,
            ]),
        );

        return true;
    }

    /**
     * The same conditions as app\components\helpers\Battle3SeriesWin::condition() at the time of writing.
     * They are copied here so that this migration does not depend on future changes of the helper.
     *
     * @return array<mixed>
     */
    private function seriesWinCondition(): array
    {
        $lobby = fn (string $key): array => [
            '{{%battle3}}.[[lobby_id]]' => new Query()
                ->select(['id'])
                ->from('{{%lobby3}}')
                ->andWhere(['key' => $key]),
        ];
        $win = [
            '{{%battle3}}.[[result_id]]' => new Query()
                ->select(['id'])
                ->from('{{%result3}}')
                ->andWhere(['key' => 'win']),
        ];

        return ['or',
            ['and',
                $lobby('bankara_challenge'),
                $win,
                ['{{%battle3}}.[[challenge_win]]' => 5],
            ],
            ['and',
                $lobby('xmatch'),
                $win,
                ['{{%battle3}}.[[challenge_win]]' => 3],
                ['>=', '{{%battle3}}.[[x_power_before]]', 2000],
                ['{{%battle3}}.[[version_id]]' => new Query()
                    ->select(['id'])
                    ->from('{{%splatoon_version3}}')
                    ->andWhere(['>=', 'release_at', new Query()
                        ->select(['release_at'])
                        ->from('{{%splatoon_version3}}')
                        ->andWhere(['tag' => '6.0.0']),
                    ]),
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->truncateTable('{{%user_badge3_series_win}}');

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
}
