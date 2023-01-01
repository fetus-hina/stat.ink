<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151112_123028_map_area extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{map}} ADD COLUMN [[area]] INTEGER');
        parent::up();
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{map}} DROP COLUMN [[area]]');
    }

    public function safeUp()
    {
        $list = [
            'arowana' => 2021,
            'bbass' => 1528,
            'dekaline' => 2465,
            'hakofugu' => 1600,
            'hirame' => 2375,
            'hokke' => 1854,
            'masaba' => 2100,
            'mongara' => 2316,
            'mozuku' => 2125,
            'negitoro' => 1665,
            'shionome' => 1900,
            'tachiuo' => 1760,
        ];
        foreach ($list as $key => $area) {
            $this->update('map', ['area' => $area], ['key' => $key]);
        }
    }
}
