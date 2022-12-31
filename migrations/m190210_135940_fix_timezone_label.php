<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190210_135940_fix_timezone_label extends Migration
{
    public function safeUp()
    {
        $this->update(
            'timezone',
            ['name' => 'Brazil (Western Amazonas)'],
            ['identifier' => 'America/Eirunepe'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'timezone',
            ['name' => 'Brazil (Weastern Amazonas)'],
            ['identifier' => 'America/Eirunepe'],
        );
    }
}
