<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181005_070721_salmon_water_level extends Migration
{
    public function up()
    {
        $this->createTable('salmon_water_level2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull(),
            'splatnet' => $this->string(32),
        ]);
        $this->batchInsert('salmon_water_level2', ['key', 'name', 'splatnet'], [
            ['low', 'Low Tide', 'low'],
            ['normal', 'Mid Tide', 'normal'],
            ['high', 'High Tide', 'high'],
        ]);
    }

    public function down()
    {
        $this->dropTable('salmon_water_level2');
    }
}
