<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;

final class m230830_223858_salmon3_user_stats_event extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon3_user_stats_event}}', [
            'user_id' => $this->pkRef('{{%user}}')->notNull(),
            'map_id' => $this->pkRef('{{%salmon_map3}}')->notNull(),
            'tide_id' => $this->pkRef('{{%salmon_water_level2}}')->notNull(),
            'event_id' => $this->pkRef('{{%salmon_event3}}')->null(),
            'waves' => $this->bigInteger()->notNull(),
            'cleared' => $this->bigInteger()->notNull(),
            'total_quota' => $this->bigInteger()->notNull(),
            'total_delivered' => $this->bigInteger()->notNull(),

            // NO PRIMARY KEY
            'UNIQUE ([[user_id]], [[map_id]], [[tide_id]], [[event_id]])',
        ]);

        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $this->execute(
            vsprintf('CREATE UNIQUE INDEX %s ON %s ( %s ) WHERE ( %s )', [
                $db->quoteColumnName('ix_salmon3_user_stats_event_1'),
                $db->quoteTableName('{{%salmon3_user_stats_event}}'),
                implode(', ', [
                    $db->quoteColumnName('user_id'),
                    $db->quoteColumnName('map_id'),
                    $db->quoteColumnName('tide_id'),
                ]),
                vsprintf('%s IS NULL', [
                    $db->quoteColumnName('event_id'),
                ]),
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%salmon3_user_stats_event}}');

        return true;
    }
}
