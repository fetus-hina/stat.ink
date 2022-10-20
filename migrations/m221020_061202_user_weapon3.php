<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221020_061202_user_weapon3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%user_weapon3}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'weapon_id' => $this->pkRef('{{%weapon3}}')->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'last_used_at' => $this->timestampTZ()->notNull(),

            'PRIMARY KEY ([[user_id]], [[weapon_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_weapon3}}');

        return true;
    }
}
