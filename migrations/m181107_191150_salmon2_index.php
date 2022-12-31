<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181107_191150_salmon2_index extends Migration
{
    public function up()
    {
        $this->createIndex(
            'ix_salmon2_user_id',
            'salmon2',
            ['user_id', 'id'],
            true,
        );
        $this->createIndex(
            'ix_salmon2_splatnet_number',
            'salmon2',
            ['user_id', 'splatnet_number'],
            false,
        );
        $this->execute('VACUUM ANALYZE {{salmon2}}');
    }

    public function down()
    {
        $this->dropIndex('ix_salmon2_splatnet_number', 'salmon2');
        $this->dropIndex('ix_salmon2_user_id', 'salmon2');
    }
}
