<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m221108_120036_boss_salmonid extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_boss3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'name' => $this->string(64)->notNull(),
        ]);

        $this->createTable('{{%salmon_boss3_alias}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'salmonid_id' => $this->pkRef('{{%salmon_boss3}}')->notNull(),
        ]);

        $data = $this->getData();
        $this->batchInsert('{{%salmon_boss3}}', ['key', 'name'], array_map(
            fn (string $key, string $name): array => [$key, $name],
            array_keys($data),
            array_values($data),
        ));

        $this->batchInsert('{{%salmon_boss3_alias}}', ['key', 'salmonid_id'], array_filter(
            array_map(
                function (string $origKey, string $name): ?array {
                    $newKey = self::name2key3($name);
                    return $newKey === $origKey
                        ? null
                        : [$newKey, $this->key2id('{{%salmon_boss3}}', $origKey)];
                },
                array_keys($data),
                array_values($data),
            ),
            fn (?array $v): bool => $v !== null,
        ));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables(['{{%salmon_boss3_alias}}', '{{%salmon_boss3}}']);

        return true;
    }

    /**
     * @return array<string, string>
     */
    private function getData(): array
    {
        return [
            'tekkyu' => 'Big Shot',
            'koumori' => 'Drizzler',
            'hashira' => 'Fish Stick',
            'diver' => 'Flipper-Flopper',
            'katapad' => 'Flyfish',
            'mogura' => 'Maws',
            'teppan' => 'Scrapper',
            'nabebuta' => 'Slammin\' Lid',
            'hebi' => 'Steel Eel',
            'bakudan' => 'Steelhead',
            'tower' => 'Stinger',
            'shake_copter' => 'Chinook',
            'kin_shake' => 'Goldie',
            'grill' => 'Griller',
            'hakobiya' => 'Mothership',
            'doro_shake' => 'Mudmouth',
        ];
    }
}
