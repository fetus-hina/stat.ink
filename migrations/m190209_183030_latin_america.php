<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190209_183030_latin_america extends Migration
{
    public function safeUp()
    {
        $this->delete('timezone_group', ['name' => 'Latin America']);
        $this->batchInsert('timezone_group', ['order', 'name'], [
            [60, 'Central America'],
            [70, 'South America'],
        ]);
    }

    public function safeDown()
    {
        $this->delete('timezone_group', [
            'name' => [
                'Central America',
                'South America',
            ],
        ]);
        $this->insert('timezone_group', [
            'order' => 60,
            'name' => 'Latin America',
        ]);
    }
}
