<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\Battle3SeriesWin;
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
                    ->andWhere(Battle3SeriesWin::condition())
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
