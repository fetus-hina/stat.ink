<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m180919_184739_special_battles extends Migration
{
    public function up()
    {
        $this->createTable('special_battle2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull(),
        ]);
        $this->batchInsert('special_battle2', ['key', 'name'], [
            ['10x', '10x Battle'],
            ['100x', '100x Battle'],
        ]);
        $this->addColumn(
            'battle2',
            'special_battle_id',
            $this->pkRef('special_battle2')->null(),
        );
    }

    public function down()
    {
        $this->dropColumn('battle2', 'special_battle_id');
        $this->dropTable('special_battle2');
    }
}
