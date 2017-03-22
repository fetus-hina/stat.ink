<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m170321_195247_user_switch_friend extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{user}} ' . implode(', ', [
            'ADD COLUMN [[sw_friend_code]] CHAR(12) NULL',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{user}} ' . implode(', ', [
            'DROP COLUMN [[sw_friend_code]]',
        ]));
    }
}
