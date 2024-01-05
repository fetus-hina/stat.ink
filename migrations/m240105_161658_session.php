<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240105_161658_session extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%session}}', [
            'id' => $this->string(64)->notNull(),
            'expire' => $this->integer()->null(),
            'data' => $this->binary()->null(),

            'PRIMARY KEY ([[id]])',
        ]);

        $this->createIndex('session_expire', '{{%session}}', ['expire']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%session}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
        ];
    }
}
