<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m240531_112439_kuma_roller extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%salmon_weapon3}}', [
            'key' => 'kuma_roller',
            'name' => 'Grizzco Roller',
            'weapon_id' => null,
        ]);

        $id = $this->key2id('{{%salmon_weapon3}}', 'kuma_roller');
        $this->batchInsert('{{%salmon_weapon3_alias}}', ['weapon_id', 'key'], [
            [$id, self::name2key3('Grizzco Roller')],
            [$id, '21900'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%salmon_weapon3}}', 'kuma_roller');

        $this->delete('{{%salmon_weapon3_alias}}', ['weapon_id' => $id]);
        $this->delete('{{%salmon_weapon3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
        ];
    }
}
