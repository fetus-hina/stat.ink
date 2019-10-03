<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171103_185043_gear2 extends Migration
{
    public function safeUp()
    {
        foreach ($this->getData() as $d) {
            $this->update('gear2', ['key' => $d[1]], ['key' => $d[0]]);
        }
    }

    public function safeDown()
    {
        foreach ($this->getData() as $d) {
            $this->update('gear2', ['key' => $d[0]], ['key' => $d[1]]);
        }
    }

    public function getData(): array
    {
        return [
            ['baby_jelly_shirt_tie', 'baby_jelly_shirt_and_tie'],
            ['shirt_tie', 'shirt_and_tie'],
            ['blue_black_squidkid_iv', 'blue_and_black_squidkid_iv'],
            ['red_black_squidkid_iv', 'red_and_black_squidkid_iv'],
        ];
    }
}
