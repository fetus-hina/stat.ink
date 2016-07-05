<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160623_132227_my_kill extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle_player}} ADD COLUMN [[my_kill]] INTEGER');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle_player}} DROP COLUMN [[my_kill]]');
    }
}
