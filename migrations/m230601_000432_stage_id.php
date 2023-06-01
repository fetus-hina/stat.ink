<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230601_000432_stage_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%map3_alias}}', [
            'map_id' => $this->key2id('{{%map3}}', 'taraport'),
            'key' => '8',
        ]);

        $this->insert('{{%map3_alias}}', [
            'map_id' => $this->key2id('{{%map3}}', 'kombu'),
            'key' => '17',
        ]);

        $this->insert('{{%salmon_map3_alias}}', [
            'map_id' => $this->key2id('{{%salmon_map3}}', 'sujiko'),
            'key' => '8',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%map3_alias}}', [
            'key' => ['8', '17'],
        ]);

        $this->delete('{{%salmon_map3_alias}}', [
            'key' => '8',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3_alias}}',
            '{{%salmon_map3_alias}}',
        ];
    }
}
