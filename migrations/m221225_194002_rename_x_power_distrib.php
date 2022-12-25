<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221225_194002_rename_x_power_distrib extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->renameTable('stat_x_power_distrib', 'stat_x_power_distrib3');
        $this->renameTable('stat_x_power_distrib_abstract', 'stat_x_power_distrib_abstract3');

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->renameTable('stat_x_power_distrib3', 'stat_x_power_distrib');
        $this->renameTable('stat_x_power_distrib_abstract3', 'stat_x_power_distrib_abstract');

        return true;
    }
}
