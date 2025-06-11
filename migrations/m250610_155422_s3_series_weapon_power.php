<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m250610_155422_s3_series_weapon_power extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->addColumns('{{%battle3}}', [
            'series_weapon_power_before' => $this->decimal(6, 1)->null(),
            'series_weapon_power_after' => $this->decimal(6, 1)->null(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropColumns('{{%battle3}}', [
            'series_weapon_power_before',
            'series_weapon_power_after',
        ]);

        return true;
    }
}
