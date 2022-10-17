<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221015_063018_user_stats extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createIndex('rank3_rank_key', '{{%rank3}}', 'rank', true);
        $this->createTable('{{%user_stat3}}', [
            'id' => $this->bigInteger()->notNull(), // should be ((user_id << 32) | (lobby_id ?? 0))
            'user_id' => $this->pkRef('{{%user}}')->notNull(),
            'lobby_id' => $this->pkRef('{{%lobby3}}')->null(),
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
            'peak_rank_id' => $this->pkRef('{{%rank3}}')->null(),
            'peak_s_plus' => $this->integer()->null(),
            'peak_x_power' => $this->decimal(6, 1)->null(),
            'peak_fest_power' => $this->decimal(6, 1)->null(),
            'peak_season' => $this->date()->null(),
            'current_rank_id' => $this->pkRef('{{%rank3}}')->null(),
            'current_s_plus' => $this->integer()->null(),
            'current_x_power' => $this->decimal(6, 1)->null(),
            'current_season' => $this->date()->null(),
            'updated_at' => $this->timestampTZ()->notNull(),

            'PRIMARY KEY ([[id]])',
            'UNIQUE ([[user_id]], [[lobby_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_stat3}}');
        $this->dropIndex('rank3_rank_key', '{{%rank3}}');

        return true;
    }
}
