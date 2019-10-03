<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m180920_192936_lobby2_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('lobby2', [
            'key' => 'fest_normal',
            'name' => 'Splatfest (Normal)',
        ]);
    }

    public function safeDown()
    {
        $this->delete('lobby2', ['key' => 'fest_normal']);
    }
}
