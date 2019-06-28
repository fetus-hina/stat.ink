<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181025_141312_index extends Migration
{
    public function up()
    {
        $this->createIndex('ix_salmon_player2_work_id', 'salmon_player2', 'work_id');
    }

    public function down()
    {
        $this->dropIndex('ix_salmon_player2_work_id', 'salmon_player2');
    }
}
