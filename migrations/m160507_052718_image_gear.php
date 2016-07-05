<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160507_052718_image_gear extends Migration
{
    public function safeUp()
    {
        $this->insert('battle_image_type', [
            'id' => 3,
            'name' => 'ギア',
        ]);
    }

    public function safeDown()
    {
        $this->delete('battle_image_type', ['id' => 3]);
    }
}
