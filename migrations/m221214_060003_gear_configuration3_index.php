<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221214_060003_gear_configuration3_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createIndex(
            'gear_configuration_secondary3_config_id',
            '{{%gear_configuration_secondary3}}',
            ['config_id', 'id'],
            unique: true,
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex(
            'gear_configuration_secondary3_config_id',
            '{{%gear_configuration_secondary3}}',
        );

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%gear_configuration_secondary3}}',
        ];
    }
}
