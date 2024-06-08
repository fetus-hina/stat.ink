<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240608_062522_fix_triumvirate_splatnet_key extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // https://github.com/fetus-hina/stat.ink/issues/1297
        $this->update(
            '{{%salmon_king3_alias}}',
            ['key' => '30'],
            ['key' => '26'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update(
            '{{%salmon_king3_alias}}',
            ['key' => '26'],
            ['key' => '30'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_king3_alias}}',
        ];
    }
}
