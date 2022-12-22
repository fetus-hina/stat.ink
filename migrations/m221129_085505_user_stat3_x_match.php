<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221129_085505_user_stat3_x_match extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%user_stat3_x_match}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'agg_battles' => $this->bigInteger()->notNull(),
            'agg_seconds' => $this->bigInteger()->notNull(),
            'wins' => $this->bigInteger()->notNull(),
            'kills' => $this->bigInteger()->notNull(),
            'assists' => $this->bigInteger()->notNull(),
            'deaths' => $this->bigInteger()->notNull(),
            'specials' => $this->bigInteger()->notNull(),
            'inked' => $this->bigInteger()->notNull(),
            'max_inked' => $this->bigInteger()->notNull(),
            'peak_x_power' => $this->decimal(6, 1)->null(),
            'peak_season' => $this->date()->null(),
            'current_x_power' => $this->decimal(6, 1)->null(),
            'current_season' => $this->date()->null(),
            'updated_at' => $this->timestampTZ(0)->notNull(),

            'PRIMARY KEY ([[user_id]], [[rule_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_stat3_x_match}}');

        return true;
    }
}
