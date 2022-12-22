<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221116_033350_salmon_wave3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_wave3}}', [
            'id' => $this->bigPrimaryKey(),
            'salmon_id' => $this->bigPkRef('{{%salmon3}}')->notNull(),
            'wave' => $this->integer()->notNull(),
            'tide_id' => $this->pkRef('{{%salmon_water_level2}}')->null(),
            'event_id' => $this->pkRef('{{%salmon_event3}}')->null(),
            'golden_quota' => $this->integer()->null(),
            'golden_delivered' => $this->integer()->null(),
            'golden_appearances' => $this->integer()->null(),

            'UNIQUE ([[salmon_id]], [[wave]])',
        ]);

        $this->createTable('{{%salmon_special_use3}}', [
            'wave_id' => $this->bigPkRef('{{%salmon_wave3}}')->notNull(),
            'special_id' => $this->pkRef('{{%special3}}')->notNull(),
            'count' => $this->integer()->notNull(),

            'PRIMARY KEY ([[wave_id]], [[special_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables(['{{%salmon_special_use3}}', '{{%salmon_wave3}}']);

        return true;
    }
}
