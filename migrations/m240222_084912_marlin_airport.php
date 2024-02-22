<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240222_084912_marlin_airport extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%map3}}', [
            'key' => 'kajiki',
            'name' => 'Marlin Airport',
            'short_name' => 'Airport',
            'area' => null,
            'release_at' => '2024-03-01T00:00:00+00:00',
            'bigrun' => false,
        ]);

        $id = $this->key2id('{{%map3}}', 'kajiki');
        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [$id, 'airport'],
            [$id, 'marlin_airport'],
            [$id, '23'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%map3}}', 'kajiki');
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
