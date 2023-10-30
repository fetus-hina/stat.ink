<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m220811_063106_special3 extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upTables();
        $this->upData();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%special3_alias}}',
            '{{%special3}}',
        ]);

        return true;
    }

    private function upTables(): void
    {
        $this->createTable('{{%special3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3(),
            'name' => $this->string(48)->notNull()->unique(),
        ]);

        $this->createTable('{{%special3_alias}}', [
            'id' => $this->primaryKey(),
            'special_id' => $this->pkRef('{{%special3}}')->notNull(),
            'key' => $this->apiKey3(),
            'UNIQUE ([[special_id]], [[key]])',
        ]);
    }

    private function upData(): void
    {
        $data = $this->getData();
        $this->batchInsert('{{%special3}}', ['key', 'name'], array_values(
            array_map(
                fn (string $key, string $name): array => [$key, $name],
                array_keys($data),
                array_values($data),
            ),
        ));

        $this->batchInsert('{{%special3_alias}}', ['special_id', 'key'], array_values(
            array_map(
                fn (string $key, string $name): array => [
                    $this->key2id('{{%special3}}', $key),
                    self::name2key3($name),
                ],
                array_keys($data),
                array_values($data),
            ),
        ));
    }

    /**
     * @return array<string, string>
     */
    private function getData(): array
    {
        return [
            'amefurashi' => 'Ink Storm',
            'energystand' => 'Tacticooler',
            'greatbarrier' => 'Big Bubbler',
            'hopsonar' => 'Wave Breaker',
            'jetpack' => 'Inkjet',
            'kanitank' => 'Crab Tank',
            'kyuinki' => 'Ink Vac',
            'megaphone51' => 'Killer Wail 5.1',
            'missile' => 'Tenta Missiles',
            'nicedama' => 'Booyah Bomb',
            'sameride' => 'Reefslider',
            'shokuwander' => 'Zipcaster',
            'tripletornado' => 'Triple Inkstrike',
            'ultrahanko' => 'Ultra Stamp',
            'ultrashot' => 'Trizooka',
        ];
    }
}
