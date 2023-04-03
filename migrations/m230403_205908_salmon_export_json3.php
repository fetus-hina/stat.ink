<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230403_205908_salmon_export_json3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_export_json3}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'last_battle_id' => $this->bigPkRef('{{%salmon3}}')->notNull(),
            'updated_at' => $this->timestampTZ(0)->notNull(),

            'PRIMARY KEY ([[user_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%salmon_export_json3}}');

        return true;
    }
}
