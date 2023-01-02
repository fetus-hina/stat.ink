<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181006_113209_salmon_weapons extends Migration
{
    public function safeUp()
    {
        foreach ($this->getData() as $k => $v) {
            $this->update(
                'salmon_main_weapon2',
                ['splatnet' => $v],
                ['key' => $k],
            );
        }
    }

    public function safeDown()
    {
        $this->update(
            'salmon_main_weapon2',
            ['splatnet' => null],
            ['key' => array_keys($this->getData())],
        );
    }

    public function getData(): array
    {
        return [
            'kuma_blaster' => 20000,
            'kuma_brella' => 20010,
            'kuma_charger' => 20020,
            'kuma_slosher' => 20030,
        ];
    }
}
