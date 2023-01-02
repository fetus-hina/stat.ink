<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171108_191604_version_group extends Migration
{
    public function up()
    {
        $this->createTable('splatoon_version_group2', [
            'id' => $this->primaryKey(),
            'tag' => $this->string(16)->unique(),
            'name' => $this->string(32)->unique(),
        ]);
        $this->batchInsert('splatoon_version_group2', ['tag', 'name'], [
            ['0.0', 'Prerelease'],
            ['1.0', 'Initial Release'],
            ['1.2', '1.2.x'],
            ['1.3', '1.3.x'],
            ['1.4', '1.4.x'],
        ]);
    }

    public function down()
    {
        $this->dropTable('splatoon_version_group2');
    }
}
