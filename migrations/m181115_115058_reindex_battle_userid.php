<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181115_115058_reindex_battle_userid extends Migration
{
    public function up()
    {
        $this->dropIndex('ix_battle_1', 'battle');
        $this->createIndex('ix_battle_1', 'battle', ['user_id', 'id'], true);
        $this->execute('VACUUM ANALYZE {{battle}}');
    }

    public function down()
    {
        $this->dropIndex('ix_battle_1', 'battle');
        $this->createIndex('ix_battle_1', 'battle', 'user_id', false);
        $this->execute('VACUUM ANALYZE {{battle}}');
    }
}
