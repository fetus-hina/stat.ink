<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221001_072215_vsstage_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], array_map(
            fn (string $key, int $vsId): array => [$this->key2id('{{%map3}}', $key), (string)$vsId],
            array_keys($this->getData()),
            array_values($this->getData())
        ));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%map3_alias}}', [
            'key' => array_map(
                fn (int $vsId): string => (string)$vsId,
                array_values($this->getData())
            ),
        ]);

        return true;
    }

    /**
     * @return array<string, int>
     */
    public function getData(): array
    {
        return [
            'amabi' => 13,
            'chozame' => 14,
            'gonzui' => 2,
            'kinmedai' => 11,
            'mahimahi' => 12,
            'masaba' => 10,
            'mategai' => 4,
            'namero' => 6,
            'sumeshi' => 16,
            'yagara' => 3,
            'yunohana' => 1,
            'zatou' => 15,
        ];
    }
}
