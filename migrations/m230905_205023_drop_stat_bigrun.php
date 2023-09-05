<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230905_205023_drop_stat_bigrun extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropTables([
            '{{%stat_bigrun_distrib3}}',
            '{{%stat_bigrun_distrib_abstract3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m230905_205023_drop_stat_bigrun cannot be reverted.\n";
        return false;
    }
}
