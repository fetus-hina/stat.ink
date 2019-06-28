<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181003_132513_salmon_title2 extends Migration
{
    public function up()
    {
        $this->createTable('salmon_title2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(64)->notNull(),
            'splatnet' => $this->integer()->null(),
        ]);
        $this->batchInsert('salmon_title2', ['key', 'name', 'splatnet'], [
            ['intern', 'Intern', 0],
            ['apprentice', 'Apprentice', 1],
            ['part_timer', 'Part-Timer', 2],
            ['go_getter', 'Go-Getter', 3],
            ['overachiever', 'Overachiever', 4],
            ['profreshional', 'Profreshional', 5],
        ]);
    }
    
    public function down()
    {
        $this->dropTable('salmon_title2');
    }
}
