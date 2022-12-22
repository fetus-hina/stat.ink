<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221115_073901_fix_fog_rog extends Migration
{
    public function vacuumTables(): array
    {
        return ['{{%salmon_event3}}'];
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update(
            '{{%salmon_event3}}',
            ['key' => 'fog'],
            ['key' => 'rog'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update(
            '{{%salmon_event3}}',
            ['key' => 'rog'],
            ['key' => 'fog'],
        );

        return true;
    }
}
