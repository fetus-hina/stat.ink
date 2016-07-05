<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m151126_103013_index extends Migration
{
    public function up()
    {
        $this->createIndex('ix_battle_1', 'battle', 'user_id');
        $this->createIndex('ix_battle_2', 'battle', 'at');
        $this->createIndex('ix_battle_image_1', 'battle_image', 'battle_id');
        $this->createIndex('ix_battle_player_1', 'battle_player', 'battle_id');
    }

    public function down()
    {
        $this->dropIndex('ix_battle_1', 'battle');
        $this->dropIndex('ix_battle_2', 'battle');
        $this->dropIndex('ix_battle_image_1', 'battle_image');
        $this->dropIndex('ix_battle_player_1', 'battle_player');
    }
}
