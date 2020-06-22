<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m200621_120537_entire_weapon2 extends Migration
{
    public function safeUp()
    {
        $tables = $this->tables;
        array_walk($tables, fn ($def, $name) => $this->createTable($name, $def));

        if (true) {
            echo "\n";
            foreach (array_keys($tables) as $name) {
                vprintf("./yii gii/model --tableName=%s --modelClass=%s --interactive=0 --overwrite\n", [
                    escapeshellarg($name),
                    escapeshellarg(implode('', array_map('ucfirst', explode('_', $name)))),
                ]);
            }
        }
    }

    public function safeDown()
    {
        $this->dropTables(array_keys($this->tables));
    }

    public function getTables(): array
    {
        $tables = [];

        foreach ($this->versionMap as $vTableSuffix => $vRefs) {
            foreach ($this->weaponMap as $wTableSuffix => $wRefs) {
                $tableName = ($vTableSuffix === '')
                    ? sprintf('stat_weapon2_entire_%s', $wTableSuffix)
                    : sprintf('stat_weapon2_entire_%s_by_%s', $wTableSuffix, $vTableSuffix);
                $def = array_merge(
                    ['rule_id' => $this->pkRef('rule2')->notNull()],
                    $vRefs
                        ? array_map(fn ($_) => $this->pkRef($_[0], $_[1])->notNull(), $vRefs)
                        : [],
                    $wRefs
                        ? array_map(fn ($_) => $this->pkRef($_[0], $_[1])->notNull(), $wRefs)
                        : [],
                    [
                        'battles' => $this->bigInteger()->notNull(),
                        'wins' => $this->bigInteger()->notNull(),
                        'avg_kill' => $this->double()->notNull(),
                        'med_kill' => $this->double()->notNull(),
                        'stddev_kill' => $this->double()->null(),
                        'avg_death' => $this->double()->notNull(),
                        'med_death' => $this->double()->notNull(),
                        'stddev_death' => $this->double()->null(),
                        'avg_special' => $this->double()->notNull(),
                        'med_special' => $this->double()->notNull(),
                        'stddev_special' => $this->double()->null(),
                        'avg_point' => $this->double()->notNull(),
                        'med_point' => $this->double()->notNull(),
                        'stddev_point' => $this->double()->null(),
                        'avg_time' => $this->double()->notNull(),
                        'updated_at' => $this->timestampTZ()->notNull(),
                    ],
                    [
                        vsprintf('PRIMARY KEY(%s)', implode(', ', array_map(
                            fn ($_) => "[[{$_}]]",
                            array_filter(array_merge(
                                ['rule_id'],
                                array_keys($vRefs ?? []),
                                array_keys($wRefs ?? []),
                            )),
                        ))),
                    ],
                );
                $tables[$tableName] = $def;
            }
        }

        return $tables;
    }

    public function getVersionMap(): array
    {
        return [
            '' => null,
            'version' => [
                'version_id' => ['splatoon_version2', 'id'],
            ],
            'vgroup' => [
                'version_group_id' => ['splatoon_version_group2', 'id'],
            ],
        ];
    }

    public function getWeaponMap(): array
    {
        return [
            'main' => [
                'weapon_id' => ['weapon2', 'id'],
            ],
            'sub' => [
                'subweapon_id' => ['subweapon2', 'id'],
            ],
            'special' => [
                'special_id' => ['special2', 'id'],
            ],
        ];
    }
}
