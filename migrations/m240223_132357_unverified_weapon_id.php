<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240223_132357_unverified_weapon_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%weapon3_alias}}', [
            'weapon_id' => $this->key2id('{{%weapon3}}', 'order_shooter_replica'),
            'key' => '46',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete(
            '{{%weapon3_alias}}',
            ['key' => '46'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%weapon3_alias}}',
        ];
    }
}
