<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m220812_003551_subweapon3 extends Migration
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
            '{{%subweapon3_alias}}',
            '{{%subweapon3}}',
        ]);

        return true;
    }

    private function upTables(): void
    {
        $this->createTable('{{%subweapon3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3(),
            'name' => $this->string(48)->notNull()->unique(),
        ]);

        $this->createTable('{{%subweapon3_alias}}', [
            'id' => $this->primaryKey(),
            'subweapon_id' => $this->pkRef('{{%subweapon3}}')->notNull(),
            'key' => $this->apiKey3(),
            'UNIQUE ([[subweapon_id]], [[key]])',
        ]);
    }

    private function upData(): void
    {
        $data = $this->getData();
        $this->batchInsert('{{%subweapon3}}', ['key', 'name'], array_values(
            array_map(
                function (string $key, string $name): array {
                    return [$key, $name];
                },
                array_keys($data),
                array_values($data),
            ),
        ));

        $this->batchInsert('{{%subweapon3_alias}}', ['subweapon_id', 'key'], array_values(
            array_filter(
                array_map(
                    function (string $key, string $name): ?array {
                        $alias = self::name2key3($name);
                        return $key !== $alias
                            ? [
                                $this->key2id('{{%subweapon3}}', $key),
                                self::name2key3($name),
                            ]
                            : null;
                    },
                    array_keys($data),
                    array_values($data),
                ),
            ),
        ));
    }

    /**
     * @return array<string, string>
     */
    private function getData(): array
    {
        return [
            'curlingbomb' => 'Curling Bomb',
            'kyubanbomb' => 'Suction Bomb',
            'linemarker' => 'Angle Shooter',
            'poisonmist' => 'Toxic Mist',
            'quickbomb' => 'Burst Bomb',
            'splashbomb' => 'Splat Bomb',
            'splashshield' => 'Splash Wall',
            'sprinkler' => 'Sprinkler',
            'torpedo' => 'Torpedo',
        ];
    }
}
