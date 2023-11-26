<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m231124_081559_season6_map extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%map3}}', ['key', 'name', 'short_name', 'release_at', 'bigrun'], [
            [
                'negitoro',
                'Bluefin Depot',
                'Depot',
                '2023-12-01T00:00:00+00:00',
                false,
            ],
            [
                'baigai',
                'Robo ROM-en',
                'ROM-en',
                '2023-12-01T00:00:00+00:00',
                false,
            ],
        ]);

        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [
                $this->key2id('{{%map3}}', 'negitoro'),
                'depot',
            ],
            [
                $this->key2id('{{%map3}}', 'negitoro'),
                self::name2key3('Bluefin Depot'),
            ],
            [
                $this->key2id('{{%map3}}', 'baigai'),
                'rom_en',
            ],
            [
                $this->key2id('{{%map3}}', 'baigai'),
                self::name2key3('Robo ROM-en'),
            ],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $ids = [
            $this->key2id('{{%map3}}', 'negitoro'),
            $this->key2id('{{%map3}}', 'baigai'),
        ];
        $this->delete('{{%map3_alias}}', ['map_id' => $ids]);
        $this->delete('{{%map3}}', ['id' => $ids]);

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
        ];
    }
}
