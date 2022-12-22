<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221117_013447_salmon_boss3_id_alias extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $insert = [];
        foreach ($this->getList() as $key => $idNum) {
            $insert[] = [(string)$idNum, $this->key2id('{{%salmon_boss3}}', $key)];
        }
        $this->batchInsert('{{%salmon_boss3_alias}}', ['key', 'salmonid_id'], $insert);

        $this->insert('{{%salmon_king3_alias}}', [
            'key' => '23',
            'salmonid_id' => $this->key2id('{{%salmon_king3}}', 'yokozuna'),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete(
            '{{%salmon_boss3_alias}}',
            [
                'key' => array_map(
                    fn (int $id): string => (string)$id,
                    array_values($this->getList()),
                ),
            ],
        );

        $this->delete('{{%salmon_king3_alias}}', ['key' => '23']);

        return true;
    }

    /**
     * @return array<string, int>
     */
    private function getList(): array
    {
        return [
            'bakudan' => 4,
            'katapad' => 5,
            'teppan' => 6,
            'hebi' => 7,
            'tower' => 8,
            'mogura' => 9,
            'koumori' => 10,
            'hashira' => 11,
            'diver' => 12,
            'tekkyu' => 13,
            'nabebuta' => 14,
            'kin_shake' => 15,
            'grill' => 17,
            'doro_shake' => 20,
            // 'yokozuna' => 23,
        ];
    }

    public function vacuumTables(): array
    {
        return [
            '{{%salmon_boss3_alias}}',
            '{{%salmon_king3_alias}}',
        ];
    }
}
