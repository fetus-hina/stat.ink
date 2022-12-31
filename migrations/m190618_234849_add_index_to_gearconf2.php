<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190618_234849_add_index_to_gearconf2 extends Migration
{
    public function safeUp()
    {
        $this->createIndex(
            'gear_configuration_secondary2_config_id',
            'gear_configuration_secondary2',
            ['config_id', 'id'],
            true, // id を含めているので unique になる
        );
    }

    protected function afterUp()
    {
        $this->execute('VACUUM ANALYZE gear_configuration_secondary2');
    }

    public function safeDown()
    {
        $this->dropIndex(
            'gear_configuration_secondary2_config_id',
            'gear_configuration_secondary2',
        );
    }
}
