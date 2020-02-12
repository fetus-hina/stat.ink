<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190623_162603_battle_created_at_index extends Migration
{
    public function safeUp()
    {
        $this->createIndex('battle_at', 'battle', 'at');
        $this->createIndex('battle2_created_at', 'battle2', 'created_at');
        $this->createIndex('salmon2_created_at', 'salmon2', 'created_at');
    }

    public function safeDown()
    {
        $this->dropIndex('battle_at', 'battle');
        $this->dropIndex('battle2_created_at', 'battle2');
        $this->dropIndex('salmon2_created_at', 'salmon2');
    }
}
