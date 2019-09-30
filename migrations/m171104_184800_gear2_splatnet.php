<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171104_184800_gear2_splatnet extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{gear2}} DROP CONSTRAINT gear2_splatnet_key');
        $this->createIndex('gear2_splatnet_key', 'gear2', ['type_id', 'splatnet'], true);
    }

    public function down()
    {
        $this->dropIndex('gear2_splatnet_key', 'gear2');
        $this->execute('ALTER TABLE {{gear2}} ADD CONSTRAINT gear2_splatnet_key UNIQUE (splatnet)');
    }
}
