<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170507_114730_battle_image2 extends Migration
{
    public function up()
    {
        $this->createTable('battle_image2', [
            'id' => $this->bigPrimaryKey(),
            'battle_id' => $this->bigPkRef('battle2'),
            'type_id' => $this->pkRef('battle_image_type'),
            'filename' => $this->string(64)->notNull()->unique(),
            'UNIQUE ([[battle_id]], [[type_id]])',
        ]);
    }

    public function down()
    {
        $this->dropTable('battle_image2');
    }
}
