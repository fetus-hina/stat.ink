<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170707_122145_special2 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('special2', ['key', 'name'], [
            ['sphere', 'Baller'],
            ['pitcher', 'Bubble Blower'],
            ['armor', 'Ink Armor'],
        ]);
    }

    public function safeDown()
    {
        $this->delete('special2', [
            'key' => [
                'sphere',
                'pitcher',
                'armor',
            ],
        ]);
    }
}
