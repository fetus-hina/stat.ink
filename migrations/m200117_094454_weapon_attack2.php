<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m200117_094454_weapon_attack2 extends Migration
{
    public function safeUp()
    {
        $this->createTable('weapon_attack2', [
            'weapon_id' => $this->pkRef('weapon2')->notNull(),
            'version_id' => $this->pkRef('splatoon_version2')
                ->defaultValue($this->getDefaultVersionId())
                ->notNull(),
            'damage' => $this->decimal(4, 1)->notNull(),
            'damage2' => $this->decimal(4, 1)->null(),
            'damage3' => $this->decimal(4, 1)->null(),
            'PRIMARY KEY ([[weapon_id]], [[version_id]])',
        ]);

        $f = fn (?float $value): ?string => $value === null ? null : sprintf('%.1f', $value);

        $data = [];
        foreach ($this->getData() as $key => $damages) {
            $damages = (array)$damages;
            foreach ($this->getWeaponIdsFromMainKey($key) as $id) {
                $data[] = [
                    (int)$id,
                    $f($damages[0] ?? null),
                    $f($damages[1] ?? null),
                    $f($damages[2] ?? null),
                ];
            }
        }

        $this->batchInsert(
            'weapon_attack2',
            ['weapon_id', 'damage', 'damage2', 'damage3'],
            $data
        );
    }

    public function safeDown()
    {
        $this->dropTable('weapon_attack2');
    }

    protected function getDefaultVersionId(): int
    {
        $versions = array_filter(
            (new Query())
                ->select(['id', 'tag'])
                ->from('splatoon_version2')
                ->all(),
            fn (array $row): bool => version_compare($row['tag'], '1.0.0', '>=')
        );
        usort($versions, fn (array $a, array $b): int => version_compare($a['tag'], $b['tag']));
        return (int)array_shift($versions)['id'];
    }

    protected function getData(): array
    {
        return [
            '52gal' => 52.0,
            '96gal' => 62.0,
            'bold' => 38.0,
            'bottlegeyser' => [38.0, 30.0],
            'jetsweeper' => 32.0,
            'nzap85' => 28.0,
            'prime' => 42.0,
            'promodeler_mg' => 24.0,
            'sharp' => 28.0,
            'sshooter' => 35.0,
            'wakaba' => 28.0,
            'h3reelgun' => 41.0,
            'l3reelgun' => 29.0,
            'clashblaster' => [60.0, 30.0],
            'hotblaster' => [125.0, 70.0],
            'longblaster' => [125.0, 70.0],
            'nova' => [125.0, 70.0],
            'rapid' => [85.0, 35.0],
            'rapid_elite' => [85.0, 35.0],
            'carbon' => [100.0, 120.0, 70.0],
            'dynamo' => [180.0, 180.0, 125.0],
            'splatroller' => [150.0, 150.0, 125.0],
            'variableroller' => [150.0, 150.0, 125.0],
            'hokusai' => [40.0, null, 25.0],
            'pablo' => [30.0, null, 20.0],
            'bamboo14mk1' => [85.0, 85.0, 30.0],
            'liter4k' => [180.0, 80.0, 40.0],
            'soytuber' => [180.0, 130.0, 40.0],
            'splatcharger' => [160.0, 80.0, 40.0],
            'squiclean_a' => [140.0, 70.0, 40.0],
            'barrelspinner' => 30.0,
            'hydra' => 40.0,
            'kugelschreiber' => [30.0, 28.0],
            'nautilus47' => 32.0,
            'splatspinner' => 32.0,
            'bucketslosher' => 70.0,
            'explosher' => [55.0, 35.0],
            'furo' => 30.0,
            'hissen' => 62.0,
            'screwslosher' => [76.0, 38.0],
            'campingshelter' => [17.0, 30.0],
            'parashelter' => [18.0, 30.0],
            'spygadget' => [12.0, 15.0],
            'dualsweeper' => 28.0,
            'kelvin525' => [36.0, 52.5],
            'maneuver' => 30.0,
            'quadhopper_black' => 28.0,
            'sputtery' => 36.0,
        ];
    }

    private function getWeaponIdsFromMainKey(string $key): array
    {
        $q = (new Query())
            ->select([
                'id' => 'w.id',
            ])
            ->from(['mw' => 'weapon2'])
            ->innerJoin(['w' => 'weapon2'], 'mw.id = w.main_group_id')
            ->andWhere(['mw.key' => $key])
            ->orderBy(['w.id' => SORT_ASC]);
        return ArrayHelper::getColumn($q->all(), 'id');
    }
}
