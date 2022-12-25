<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221224_232902_stat_x_power_distribution extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%stat_x_power_distrib}}', [
            'season_id' => $this->pkRef('season3')->notNull(),
            'rule_id' => $this->pkRef('rule3')->notNull(),
            'x_power' => $this->integer()->notNull()->check('[[x_power]] % 50 = 0'),
            'users' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[season_id]], [[rule_id]], [[x_power]])',
        ]);

        $this->createTable('{{%stat_x_power_distrib_abstract}}', [
            'season_id' => $this->pkRef('season3')->notNull(),
            'rule_id' => $this->pkRef('rule3')->notNull(),
            'users' => $this->bigInteger()->notNull(),
            'average' => $this->float()->notNull(),
            'stddev' => $this->float()->null(),
            'median' => $this->decimal(6, 1)->null(),

            'PRIMARY KEY ([[season_id]], [[rule_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_x_power_distrib_abstract}}');
        $this->dropTable('{{%stat_x_power_distrib}}');

        return true;
    }
}
