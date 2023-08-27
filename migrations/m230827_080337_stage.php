<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m230827_080337_stage extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%map3}}', ['key', 'name', 'short_name', 'release_at'], [
            [
                'takaashi',
                'Crableg Capital',
                'Capital',
                '2023-09-01T00:00:00+00:00',
            ],
            [
                'ohyo',
                'Shipshape Cargo Co.',
                'Cargo',
                '2023-09-01T00:00:00+00:00',
            ],
        ]);

        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [
                $this->key2id('{{%map3}}', 'takaashi'),
                self::name2key3('Crableg Capital'),
            ],
            [
                $this->key2id('{{%map3}}', 'takaashi'),
                self::name2key3('Capital'),
            ],
            [
                $this->key2id('{{%map3}}', 'ohyo'),
                self::name2key3('Shipshape Cargo Co.'),
            ],
            [
                $this->key2id('{{%map3}}', 'ohyo'),
                self::name2key3('Cargo'),
            ],
        ]);

        $this->insert('{{%salmon_map3}}', [
            'key' => 'tokishirazu',
            'name' => 'Salmonid Smokeyard',
            'short_name' => 'Smokeyard',
        ]);

        $this->insert('{{%salmon_map3_alias}}', [
            'map_id' => $this->key2id('{{%salmon_map3}}', 'tokishirazu'),
            'key' => self::name2key3('Salmonid Smokeyard'),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete(
            '{{%map3_alias}}',
            [
                'map_id' => [
                    $this->key2id('{{%map3}}', 'takaashi'),
                    $this->key2id('{{%map3}}', 'ohyo'),
                ],
            ],
        );

        $this->delete(
            '{{%map3}}',
            ['key' => ['takaashi', 'ohyo']],
        );

        $this->delete(
            '{{%salmon_map3_alias}}',
            ['map_id' => $this->key2id('{{%salmon_map3}}', 'tokishirazu')],
        );

        $this->delete(
            '{{%salmon_map3}}',
            ['key' => 'tokishirazu'],
        );

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
            '{{%salmon_map3}}',
            '{{%salmon_map3_alias}}',
        ];
    }
}
