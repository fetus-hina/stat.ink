<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m190616_194247_bomb_pitcher extends Migration
{
    public function safeUp()
    {
        $this->alterColumn(
            'special2',
            'key',
            $this->string(32)->notNull(),
        );

        $newData = $this->getNewData();
        $this->batchInsert('special2', ['key', 'name'], array_map(
            function (string $key, array $values): array {
                return [$key, $values['name']];
            },
            array_keys($newData),
            array_values($newData),
        ));

        $ids = $this->getSpecialIDs();
        foreach ($newData as $key => $data) {
            if (!$this->checkCurrentSpecial($data['weapons'], $ids['pitcher'])) {
                return false;
            }

            $this->update(
                'weapon2',
                ['special_id' => $ids[$key]],
                ['key' => $data['weapons']],
            );
        }
    }

    public function safeDown()
    {
        $ids = $this->getSpecialIDs();
        $this->update(
            'weapon2',
            ['special_id' => $ids['pitcher']],
            ['special_id' => array_values($ids)],
        );

        $this->delete('special2', [
            'key' => array_keys($this->getNewData()),
        ]);

        $this->alterColumn(
            'special2',
            'key',
            $this->string(16)->notNull(),
        );
    }

    public function getNewData(): array
    {
        // {{{
        return [
            'curlingbomb_pitcher' => [
                'name' => 'Curling-Bomb Launcher',
                'weapons' => [
                    'campingshelter_sorella',
                    'promodeler_mg',
                ],
            ],
            'kyubanbomb_pitcher' => [
                'name' => 'Suction-Bomb Launcher',
                'weapons' => [
                    'furo_deco',
                    'nova_neo',
                    'sharp_neo',
                    'splatcharger_collabo',
                    'splatscope_collabo',
                    'sputtery',
                ],
            ],
            'quickbomb_pitcher' => [
                'name' => 'Burst-Bomb Launcher',
                'weapons' => [
                    'bamboo14mk2',
                    'bucketslosher_soda',
                ],
            ],
            'robotbomb_pitcher' => [
                'name' => 'Autobomb Launcher',
                'weapons' => [
                    'carbon_deco',
                    'quadhopper_white',
                ],
            ],
            'splashbomb_pitcher' => [
                'name' => 'Splat-Bomb Launcher',
                'weapons' => [
                    'parashelter_sorella',
                    'rapid',
                    'screwslosher_neo',
                    'variableroller',
                ],
            ],
        ];
        // }}}
    }

    public function getSpecialIDs(): array
    {
        // safeDown の実装でこの値の一覧に該当するものを全て pitcher に戻すので
        // 関係のないスペシャルをここの値に含めてはいけない

        $keys = array_merge(array_keys($this->getNewData()), ['pitcher']);
        return ArrayHelper::map(
            (new Query())->select('*')->from('special2')->andWhere(['key' => $keys])->all(),
            'key',
            'id',
        );
    }

    protected function checkCurrentSpecial(array $weaponKeys, int $requiredId): bool
    {
        $current = ArrayHelper::map(
            (new Query())->select('*')->from('weapon2')->andWhere(['key' => $weaponKeys])->all(),
            'key',
            'special_id',
        );
        $results = true;
        foreach ($weaponKeys as $key) {
            if (!isset($current[$key])) {
                vfprintf(STDERR, "%s: weapon does not exists: %s\n", [
                    __METHOD__,
                    $key,
                ]);
                $results = false;
            } elseif ($current[$key] != $requiredId) {
                vfprintf(STDERR, "%s: required special = %d, current special = %d\n", [
                    __METHOD__,
                    $requiredId,
                    $current[$key],
                ]);
                $results = false;
            }
        }
        return $results;
    }
}
