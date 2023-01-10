<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230110_162213_stat_kd_win3_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createIndex(
            'stat_kd_win_rate3_season_lobby_kill_death',
            '{{%stat_kd_win_rate3}}',
            ['season_id', 'lobby_id', 'kills', 'deaths'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex(
            'stat_kd_win_rate3_season_lobby_kill_death',
            '{{%stat_kd_win_rate3}}',
        );

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%stat_kd_win_rate3}}',
        ];
    }
}
