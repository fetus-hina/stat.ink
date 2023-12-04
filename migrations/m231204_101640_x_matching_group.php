<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m231204_101640_x_matching_group extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%x_matching_group_version3}}', [
            'id' => 2,
            'minimum_version' => '6.0.0',
        ]);

        $this->batchInsert('{{%x_matching_group3}}', ['name', 'short_name', 'color', 'rank'], [
            ['Short', 'S', self::rgb(255, 202, 191), 2010],
            ['Middle', 'M', self::rgb(255, 255, 128), 2020],
            ['Long', 'L', self::rgb(119, 217, 168), 2030],
            ['Chargers', 'C', self::rgb(191, 228, 255), 2040],
        ]);

        $this->batchInsert(
            '{{%x_matching_group_weapon3}}',
            ['version_id', 'weapon_id', 'group_id'],
            iterator_to_array(self::getData(2)),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%x_matching_group_weapon3}}', [
            'version_id' => 2,
        ]);

        $this->delete('{{%x_matching_group3}}', [
            'name' => [
                'Short',
                'Middle',
                'Long',
                'Chargers',
            ],
        ]);

        $this->delete('{{%x_matching_group_version3}}', [
            'minimum_version' => '6.0.0',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%x_matching_group_version3}}',
        ];
    }

    private static function rgb(int $r, int $g, int $b): string
    {
        return vsprintf('%02x%02x%02x', [$r, $g, $b]);
    }

    /**
     * @return Generator<int, array{int, int, int}>
     */
    private static function getData(int $versionId): Generator
    {
        $groups = ArrayHelper::map(
            (new Query())
                ->select(['short_name', 'id'])
                ->from('{{%x_matching_group3}}')
                ->andWhere([
                    'short_name' => ['S', 'M', 'L', 'C'],
                ])
                ->all(),
            'short_name',
            'id',
        );

        $weapons = ArrayHelper::map(
            (new Query())
                ->select(['key', 'id'])
                ->from('{{%weapon3}}')
                ->all(),
            'key',
            'id',
        );

        foreach (self::getRawData() as $groupKey => $weaponKeys) {
            if (!isset($groups[$groupKey])) {
                throw new LogicException("Unknown group: {$groupKey}");
            }
            $groupId = $groups[$groupKey];
            foreach ($weaponKeys as $weaponKey) {
                if (!isset($weapons[$weaponKey])) {
                    throw new LogicException("Unknown weapon: {$weaponKey}");
                }
                yield [
                    $versionId,
                    $weapons[$weaponKey],
                    $groupId,
                ];
            }
        }
    }

    /**
     * @return array<string, string[]>
     */
    private static function getRawData(): array
    {
        return [
            'S' => [
                '52gal',
                'bold',
                'bold_neo',
                'carbon',
                'carbon_deco',
                'clashblaster',
                'clashblaster_neo',
                'drivewiper',
                'drivewiper_deco',
                'fincent',
                'fincent_hue',
                'heroshooter_replica',
                'hissen',
                'hissen_hue',
                'hokusai',
                'hokusai_hue',
                'hotblaster',
                'hotblaster_custom',
                'l3reelgun',
                'l3reelgun_d',
                'maneuver',
                'maneuver_collabo',
                'momiji',
                'nova',
                'nova_neo',
                'nzap85',
                'nzap89',
                'pablo',
                'pablo_hue',
                'parashelter',
                'parashelter_sorella',
                'promodeler_mg',
                'promodeler_rg',
                'sharp',
                'sharp_neo',
                'splatroller',
                'splatroller_collabo',
                'sputtery',
                'sputtery_hue',
                'spygadget',
                'spygadget_sorella',
                'sshooter',
                'sshooter_collabo',
                'wakaba',
                'wideroller',
                'wideroller_collabo',
            ],
            'M' => [
                '96gal',
                '96gal_deco',
                'bucketslosher',
                'bucketslosher_deco',
                'campingshelter',
                'campingshelter_sorella',
                'dualsweeper',
                'dualsweeper_custom',
                'dynamo',
                'dynamo_tesla',
                'examiner',
                'furo',
                'furo_deco',
                'h3reelgun',
                'h3reelgun_d',
                'jimuwiper',
                'jimuwiper_hue',
                'kelvin525',
                'lact450',
                'lact450_deco',
                'longblaster',
                'moprin',
                'nautilus47',
                'prime',
                'prime_collabo',
                'quadhopper_black',
                'quadhopper_white',
                'rapid',
                'rapid_deco',
                'sblast91',
                'sblast92',
                'screwslosher',
                'screwslosher_neo',
                'spaceshooter',
                'spaceshooter_collabo',
                'splatspinner',
                'splatspinner_collabo',
                'variableroller',
            ],
            'L' => [
                'barrelspinner',
                'barrelspinner_deco',
                'bottlegeyser',
                'bottlegeyser_foil',
                'explosher',
                'hydra',
                'jetsweeper',
                'jetsweeper_custom',
                'kugelschreiber',
                'kugelschreiber_hue',
                'rapid_elite',
                'rapid_elite_deco',
                'tristringer',
                'tristringer_collabo',
            ],
            'C' => [
                'bamboo14mk1',
                'liter4k',
                'liter4k_scope',
                'rpen_5b',
                'rpen_5h',
                'soytuber',
                'soytuber_custom',
                'splatcharger',
                'splatcharger_collabo',
                'splatscope',
                'splatscope_collabo',
                'squiclean_a',
            ],
        ];
    }
}
