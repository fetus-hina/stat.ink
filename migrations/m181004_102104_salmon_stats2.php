<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181004_102104_salmon_stats2 extends Migration
{
    public function up()
    {
        $this->createTable('salmon_stats2', [
            'id' => $this->primaryKey(),
            'user_id' => $this->pkRef('user')->notNull(),
            'work_count' => $this->bigInteger(),
            'total_golden_eggs' => $this->bigInteger(),
            'total_eggs' => $this->bigInteger(),
            'total_rescued' => $this->bigInteger(),
            'total_point' => $this->bigInteger(),
            'as_of' => $this->timestampTZ()->notNull(),
            'created_at' => $this->timestampTZ()->notNull(),
        ]);
        $this->createIndex('ix_salmon_stats2_1', 'salmon_stats2', ['user_id', 'as_of']);
    }

    public function down()
    {
        $this->dropTable('salmon_stats2');
    }
}
