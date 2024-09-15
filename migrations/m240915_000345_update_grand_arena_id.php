<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240915_000345_update_grand_arena_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%map3_alias}}', [
            'map_id' => $this->key2id('{{%map3}}', 'grand_arena'),
            'key' => '25',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%map3_alias}}', ['key' => '25']);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3_alias}}',
        ];
    }
}
