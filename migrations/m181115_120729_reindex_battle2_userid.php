<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181115_120729_reindex_battle2_userid extends Migration
{
    public function up()
    {
        $this->createIndex('ix_battle2_userid_id', 'battle2', ['user_id', 'id'], true);
        $this->execute('VACUUM ANALYZE {{battle2}}');
    }

    public function down()
    {
        $this->dropIndex('ix_battle2_userid_id', 'battle2');
    }
}
