<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m210124_202525_splatfest2 extends Migration
{
    public function safeUp()
    {
        $this->createTable('splatfest2', [
            'id' => $this->primaryKey(),
            'name_a' => $this->string(63)->notNull(),
            'name_b' => $this->string(63)->notNull(),
            'term' => 'TSTZRANGE NOT NULL',
            'query_term' => 'TSTZRANGE NOT NULL',
        ]);
        $this->createTable('splatfest2_region', [
            'fest_id' => $this->pkRef('splatfest2')->notNull(),
            'region_id' => $this->pkRef('region2')->notNull(),
            $this->tablePrimaryKey(['fest_id', 'region_id']),
            'UNIQUE ([[region_id]], [[fest_id]])',
        ]);
    }

    public function safeDown()
    {
        $this->dropTables(['splatfest2_region', 'splatfest2']);
    }
}
