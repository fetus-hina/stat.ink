<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181021_163348_force_blackout extends Migration
{
    public function up()
    {
        $this->createTable('force_blackout2', [
            'splatnet_id' => $this->string(16)->notNull(),
            'note' => $this->text()->null(),
            'PRIMARY KEY ([[splatnet_id]])',
        ]);
        $this->insert('force_blackout2', [
            'splatnet_id' => '97b91c651f048b43',
            'note' => 'https://twitter.com/Ad_Action7/status/1054005107728146432',
        ]);
    }

    public function down()
    {
        $this->dropTable('force_blackout2');
    }
}
