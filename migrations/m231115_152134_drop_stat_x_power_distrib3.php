<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m231115_152134_drop_stat_x_power_distrib3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropTable('{{%stat_x_power_distrib3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->createTable('{{%stat_x_power_distrib3}}', [
            'season_id' => $this->pkRef('{{%season3}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
            'x_power' => $this->integer()->notNull(),
            'users' => $this->bigInteger()->notNull(),
            'PRIMARY KEY ([[season_id]], [[rule_id]], [[x_power]])',
        ]);

        return true;
    }
}
