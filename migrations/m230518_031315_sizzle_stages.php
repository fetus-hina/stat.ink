<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m230518_031315_sizzle_stages extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%map3}}', ['key', 'name', 'short_name', 'area', 'release_at'], [
            ['taraport', 'Barnacle & Dime', 'B&D', null, '2023-06-01T00:00:00+00:00'],
            ['kombu', 'Humpback Pump Track', 'Track', null, '2023-06-01T00:00:00+00:00'],
        ]);

        $taraport = $this->key2id('{{%map3}}', 'taraport');
        $kombu = $this->key2id('{{%map3}}', 'kombu');

        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [$taraport, self::name2key3('Barnacle & Dime')],
            [$taraport, self::name2key3('B&D')],
            [$kombu, 'combu'],
            [$kombu, self::name2key3('Humpback Pump Track')],
            [$kombu, self::name2key3('Track')],
        ]);

        $this->insert('{{%salmon_map3}}', [
            'key' => 'sujiko',
            'name' => 'Jammin\' Salmon Junction',
            'short_name' => 'Junction',
        ]);

        $this->insert('{{%salmon_map3_alias}}', [
            'map_id' => $this->key2id('{{%salmon_map3}}', 'sujiko'),
            'key' => self::name2key3('Jammin\' Salmon Junction'),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $taraport = $this->key2id('{{%map3}}', 'taraport');
        $kombu = $this->key2id('{{%map3}}', 'kombu');
        $sujiko = $this->key2id('{{%salmon_map3}}', 'sujiko');

        $this->delete('{{%salmon_map3_alias}}', ['map_id' => $sujiko]);
        $this->delete('{{%salmon_map3}}', ['id' => $sujiko]);
        $this->delete('{{%map3_alias}}', ['map_id' => [$taraport, $kombu]]);
        $this->delete('{{%map3}}', ['id' => [$taraport, $kombu]]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3_alias}}',
            '{{%map3}}',
            '{{%salmon_map3_alias}}',
            '{{%salmon_map3}}',
        ];
    }
}
