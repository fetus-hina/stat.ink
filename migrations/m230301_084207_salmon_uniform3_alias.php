<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m230301_084207_salmon_uniform3_alias extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_uniform3_alias}}', [
            'id' => $this->primaryKey(),
            'uniform_id' => $this->pkRef('{{%salmon_uniform3}}')->notNull(),
            'key' => $this->apiKey3()->notNull()->unique(),
        ]);

        $insertData = [];
        foreach ($this->getData() as $key => $aliases) {
            $id = $this->key2id('{{%salmon_uniform3}}', $key);
            foreach ($aliases as $alias) {
                $insertData[] = [$id, $alias];
            }
        }

        $this->batchInsert(
            '{{%salmon_uniform3_alias}}',
            ['uniform_id', 'key'],
            $insertData,
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%salmon_uniform3_alias}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_uniform3_alias}}',
        ];
    }

    /**
     * @return array<string, string[]>
     */
    private function getData(): array
    {
        return [
            'orange' => [
                self::name2key3('Orange Slopsuit'),
                '1',
            ],
            'green' => [
                self::name2key3('Green Slopsuit'),
                '2',
            ],
            'yellow' => [
                self::name2key3('Yellow Slopsuit'),
                '3',
            ],
            'pink' => [
                self::name2key3('Pink Slopsuit'),
                '4',
            ],
            'blue' => [
                self::name2key3('Blue Slopsuit'),
                '5',
            ],
            'black' => [
                self::name2key3('Black Slopsuit'),
                '6',
            ],
            'white' => [
                self::name2key3('White Slopsuit'),
                '7',
            ],
            'orange_gloopsuit' => ['8'],
            'black_gloopsuit' => ['9'],
            'yellow_gloopsuit' => ['10'],
            'brown_gloopsuit' => ['11'],
        ];
    }
}
