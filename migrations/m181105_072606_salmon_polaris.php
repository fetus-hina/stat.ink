<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181105_072606_salmon_polaris extends Migration
{
    public function safeUp()
    {
        $this->insert('salmon_map2', [
            'key' => 'polaris',
            'name' => 'Ruins of Ark Polaris',
            'splatnet_hint' => null,
        ]);
    }

    public function safeDown()
    {
        $this->delete('salmon_map2', ['key' => 'polaris']);
    }
}
