<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230217_064404_mantamaria extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%map3}}', [
            'key' => 'manta',
            'name' => 'Manta Maria',
            'short_name' => 'Manta',
            'area' => null,
            'release_at' => '2023-03-01T00:00:00+00:00',
        ]);

        $this->insert('{{%map3_alias}}', [
            'map_id' => $this->key2id('{{%map3}}', 'manta'),
            'key' => 'manta_maria',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%map3}}', 'manta');
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
