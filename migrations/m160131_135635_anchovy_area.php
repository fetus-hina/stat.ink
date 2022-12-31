<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160131_135635_anchovy_area extends Migration
{
    public function safeUp()
    {
        $this->update('map', [
            'area' => 2293,
        ], [
            'key' => 'anchovy',
        ]);
    }

    public function safeDown()
    {
        $this->update('map', [
            'area' => null,
        ], [
            'key' => 'anchovy',
        ]);
    }
}
