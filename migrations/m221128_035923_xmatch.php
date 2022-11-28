<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221128_035923_xmatch extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%lobby_group3}}', [
            'key' => 'xmatch',
            'name' => 'X Battle',
            'rank' => 300,
            'importance' => 600,
        ]);

        $this->insert('{{%lobby3}}', [
            'key' => 'xmatch',
            'name' => 'X Battle',
            'rank' => 310,
            'group_id' => $this->key2id('{{%lobby_group3}}', 'xmatch'),
        ]);

        $this->addColumns('{{%battle3}}', [
            'x_power_before' => $this->decimal(6, 1)->null(),
            'x_power_after' => $this->decimal(6, 1)->null(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%lobby3}}', ['key' => 'xmatch']);
        $this->delete('{{%lobby_group3}}', ['key' => 'xmatch']);

        $this->dropColumns('{{%battle3}}', ['x_power_before', 'x_power_after']);

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%lobby3}}',
            '{{%lobby_group3}}',
        ];
    }
}
