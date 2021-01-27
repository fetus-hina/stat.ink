<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m210124_195729_region2 extends Migration
{
    public function safeUp()
    {
        $this->createTable('region2', [
            'id' => $this->primaryKey(),
            'key' => $this->char(2)->notNull()->unique(),
            'name' => $this->string(63)->notNull(),
        ]);
        $this->batchInsert('region2', ['key', 'name'], [
            ['jp', 'Japan'],
            ['eu', 'Europe'],
            ['na', 'North America/Oceania'],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('region2');
    }
}
