<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m231115_085522_stat_x_power_distrib_abstract3_percentile extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumns('{{%stat_x_power_distrib_abstract3}}', [
            'pct5' => $this->decimal(6, 1)->null(),
            'pct25' => $this->decimal(6, 1)->null(),
            'pct75' => $this->decimal(6, 1)->null(),
            'pct80' => $this->decimal(6, 1)->null(),
            'pct95' => $this->decimal(6, 1)->null(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumns('{{%stat_x_power_distrib_abstract3}}', [
            'pct5',
            'pct25',
            'pct75',
            'pct80',
            'pct95',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_x_power_distrib_abstract3}}',
        ];
    }
}
