<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230217_072304_kraken_royale extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%special3}}', [
            'key' => 'teioika',
            'name' => 'Kraken Royale',
            'rank' => 250,
        ]);

        $this->insert('{{%special3_alias}}', [
            'special_id' => $this->key2id('{{%special3}}', 'teioika'),
            'key' => 'kraken_royale',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%special3}}', 'teioika');
        $this->delete('{{%special3_alias}}', ['special_id' => $id]);
        $this->delete('{{%special3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%special3}}',
            '{{%special3_alias}}',
        ];
    }
}
