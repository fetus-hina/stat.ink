<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;
use yii\helpers\ArrayHelper;

final class m221103_031333_ability3 extends Migration
{
    use AutoKey;

    public function vacuumTables(): array
    {
        return ['{{%ability3}}'];
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%ability3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apikey3()->notNull()->unique(),
            'name' => $this->string(32)->notNull(),
            'rank' => $this->integer()->notNull()->unique(),
            'primary_only' => $this->boolean()->notNull(),
        ]);

        $this->batchInsert(
            '{{%ability3}}',
            ['key', 'name', 'rank', 'primary_only'],
            ArrayHelper::getColumn(
                $this->getData(),
                fn (array $item): array => [
                    (string)ArrayHelper::getValue($item, 'key'),
                    (string)ArrayHelper::getValue($item, 'name'),
                    (int)ArrayHelper::getValue($item, 'rank'),
                    (bool)ArrayHelper::getValue($item, 'primary_only'),
                ],
            ),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%ability3}}');

        return true;
    }

    private function getData(): array
    {
        $abilities1 = [
            'Ink Saver (Main)',
            'Ink Saver (Sub)',
            'Ink Recovery Up',
            'Run Speed Up',
            'Swim Speed Up',
            'Special Charge Up',
            'Special Saver',
            'Special Power Up',
            'Quick Respawn',
            'Quick Super Jump',
            'Sub Power Up',
            'Ink Resistance Up',
            'Sub Resistance Up',
            'Intensify Action',
        ];

        $abilities2 = [
            'Opening Gambit',
            'Last-Ditch Effort',
            'Tenacity',
            'Comeback',
            'Ninja Squid',
            'Haunt',
            'Thermal Ink',
            'Respawn Punisher',
            'Ability Doubler',
            'Stealth Jump',
            'Object Shredder',
            'Drop Roller',
        ];

        return array_merge(
            $this->abilityNamesToData($abilities1, 1000, false),
            $this->abilityNamesToData($abilities2, 2000, true),
        );
    }

    /**
     * @param string[] $names
     */
    private function abilityNamesToData(array $names, int $baseRank, bool $primaryOnly): array
    {
        return array_values(
            array_map(
                fn (string $name, int $i): array => $this->abilityNameToData(
                    $name,
                    $baseRank + $i * 10,
                    $primaryOnly,
                ),
                array_values($names),
                range(0, count($names) - 1),
            ),
        );
    }

    private function abilityNameToData(string $name, int $rank, bool $primaryOnly): array
    {
        $name = trim($name);

        return [
            'key' => self::name2key3($name),
            'name' => $name,
            'rank' => $rank,
            'primary_only' => $primaryOnly,
        ];
    }
}
