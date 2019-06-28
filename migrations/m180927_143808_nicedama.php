<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m180927_143808_nicedama extends Migration
{
    public function safeUp()
    {
        $this->insert('special2', [
            'key' => 'nicedama',
            'name' => 'Booyah Bomb',
        ]);
    }

    public function safeDown()
    {
        $this->delete('special2', [
            'key' => 'nicedama',
        ]);
    }
}
