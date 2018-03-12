<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m180312_085150_battle_player2_index extends Migration
{
    public function up()
    {
        $this->createIndex('ix_battle_player2_battle_id', 'battle_player2', ['battle_id']);
        $this->execute('VACUUM ANALYZE {{battle_player2}}');
    }

    public function down()
    {
        $this->dropIndex('ix_battle_player2_battle_id', 'battle_player2');
    }
}
