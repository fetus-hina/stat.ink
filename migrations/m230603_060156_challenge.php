<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230603_060156_challenge extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumns('{{%battle3}}', [
            'event_id' => $this->pkRef('{{%event3}}')->null(),
            'event_power' => $this->decimal(6, 1)->null(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumns('{{%battle3}}', [
            'event_id',
            'event_power',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%battle3}}',
        ];
    }
}
