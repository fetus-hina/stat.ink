<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m221004_105117_salmon_stage3 extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_map3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull(),
            'name' => $this->string(63)->notNull(),
            'short_name' => $this->string(63)->notNull(),
        ]);

        $this->createTable('{{%salmon_map3_alias}}', [
            'id' => $this->primaryKey(),
            'map_id' => $this->pkRef('{{%salmon_map3}}')->notNull(),
            'key' => $this->apiKey3()->notNull(),
        ]);

        $this->batchInsert('{{%salmon_map3}}', ['key', 'name', 'short_name'], [
            ['dam', 'Spawning Grounds', 'Grounds'],
            ['aramaki', 'Sockeye Station', 'Station'],
            ['meuniere', 'Gone Fission Hydroplant', 'Hydroplant'],
        ]);

        $aliases = [
            'dam' => ['1', self::name2key3('Spawning Grounds')],
            'aramaki' => ['2', self::name2key3('Sockeye Station')],
            'meuniere' => ['7', self::name2key3('Gone Fission Hydroplant')],
        ];
        $inserts = [];
        foreach ($aliases as $key => $names) {
            $mapId = $this->key2id('{{%salmon_map3}}', $key);
            foreach ($names as $name) {
                $inserts[] = [$mapId, $name];
            }
        }
        $this->batchInsert('{{%salmon_map3_alias}}', ['map_id', 'key'], $inserts);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables(['{{%salmon_map3_alias}}', '{{%salmon_map3}}']);

        return true;
    }
}
