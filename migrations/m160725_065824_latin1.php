<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160725_065824_latin1 extends Migration
{
    public function safeUp()
    {
        $this->update('charset', ['name' => 'Latin-1'], ['php_name' => 'CP1252']);
    }

    public function safeDown()
    {
        $this->update('charset', ['name' => 'Windows-1252'], ['php_name' => 'CP1252']);
    }
}
