<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240222_090129_bonerattle_arena extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%salmon_map3}}', [
            'key' => 'donpiko',
            'name' => 'Bonerattle Arena',
            'short_name' => 'Arena',
        ]);
        $id = $this->key2id('{{%salmon_map3}}', 'donpiko');
        $this->batchInsert('{{%salmon_map3_alias}}', ['map_id', 'key'], [
            [$id, 'arena'],
            [$id, 'bonerattle_arena'],
            [$id, '3'], // verify later and remove if wrong
            [$id, '5'], // verify later and remove if wrong
            [$id, '9'], // verify later and remove if wrong
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%salmon_map3}}', 'donpiko');
        $this->delete('{{%salmon_map3_alias}}', ['map_id' => $id]);
        $this->delete('{{%salmon_map3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_map3}}',
            '{{%salmon_map3_alias}}',
        ];
    }
}
