<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240910_165106_grand_splatlands_bowl extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%map3}}', [
            'key' => 'grand_arena',
            'name' => 'Grand Splatlands Bowl',
            'short_name' => 'Bowl',
            'release_at' => '2024-09-15T00:00:00+00:00',
        ]);
        $id = $this->key2id('{{%map3}}', 'grand_arena');

        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [$id, 'grand_splatlands_bowl'],
            [$id, '107'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%map3}}', 'grand_arena');

        $this->delete('{{%map3_alias}}', ['map_id' => $id]);
        $this->delete('{{%map3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3}}',
            '{{%map3_alias}}',
        ];
    }
}
