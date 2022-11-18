<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221115_201850_salmon_boss_appearance3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_boss_appearance3}}', [
            'salmon_id' => $this->bigPkRef('{{%salmon3}}')->notNull(),
            'boss_id' => $this->pkRef('{{%salmon_boss3}}')->notNull(),
            'appearances' => $this->integer()->notNull(),
            'defeated' => $this->integer()->notNull(),
            'defeated_by_me' => $this->integer()->notNull(),

            'PRIMARY KEY ([[salmon_id]], [[boss_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%salmon_boss_appearance3}}');

        return true;
    }
}
