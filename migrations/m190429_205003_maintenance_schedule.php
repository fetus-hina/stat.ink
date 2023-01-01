<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190429_205003_maintenance_schedule extends Migration
{
    public function safeUp()
    {
        $this->createTable('maintenance_schedule', [
            'id' => $this->primaryKey(),
            'reason' => $this->text()->notNull(),
            'start_at' => $this->timestampTZ(0)->notNull(),
            'end_at' => $this->timestampTZ(0)->notNull(),
            'enabled' => $this->boolean()->notNull()->defaultValue(true),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('maintenance_schedule');
    }
}
