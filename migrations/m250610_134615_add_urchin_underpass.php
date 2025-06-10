<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m250610_134615_add_urchin_underpass extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->insert('{{%map3}}', [
            'key' => 'decaline',
            'name' => 'Urchin Underpass',
            'short_name' => 'Underpass',
            'area' => null,
            'release_at' => '2025-06-12T09:00:00+09:00',
            'bigrun' => false,
        ]);

        $id = $this->key2id('{{%map3}}', 'decaline');
        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [$id, 'dekaline'], // Splatoon 1 compat.
            [$id, self::name2key3('Urchin Underpass')],
            [$id, self::name2key3('Underpass')],
            [$id, '26'], // guess
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $id = $this->key2id('{{%map3}}', 'decaline');

        $this->delete('{{%map3_alias}}', ['map_id' => $id]);
        $this->delete('{{%map3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%map3}}',
            '{{%map3_alias}}',
        ];
    }
}
