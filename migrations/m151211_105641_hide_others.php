<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m151211_105641_hide_others extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{user}} ADD COLUMN [[is_black_out_others]] BOOLEAN DEFAULT FALSE');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{user}} DROP COLUMN [[is_black_out_others]]');
    }
}
