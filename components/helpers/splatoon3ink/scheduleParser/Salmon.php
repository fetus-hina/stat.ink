<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\splatoon3ink\scheduleParser;

use app\models\Map3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use app\models\SalmonRandom3;
use app\models\SalmonWeapon3;
use app\models\api\v3\postBattle\TypeHelperTrait;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use function array_map;
use function array_values;
use function is_string;

trait Salmon
{
    use Common;
    use TypeHelperTrait;

    protected static function salmon(array $nodes, bool $isBigRun): array
    {
        return array_values(
            array_map(
                fn (array $schedule): array => self::processSalmonSchedule($schedule, $isBigRun),
                $nodes,
            ),
        );
    }

    private static function processSalmonSchedule(array $schedule, bool $isBigRun): array
    {
        return [
            'startAt' => self::parseTimestamp(ArrayHelper::getValue($schedule, 'startTime')),
            'endAt' => self::parseTimestamp(ArrayHelper::getValue($schedule, 'endTime')),
            'map_id' => self::parseSalmonStage(
                ArrayHelper::getValue($schedule, 'setting.coopStage.name'),
                $isBigRun,
            ),
            'king' => self::parseSalmonKing(
                ArrayHelper::getValue($schedule, 'setting.boss.name'),
            ),
            'weapons' => array_map(
                fn (array $info): ?ActiveRecord => self::parseSalmonWeapon($info),
                ArrayHelper::getValue($schedule, 'setting.weapons'),
            ),
            'is_random_map' => ArrayHelper::getValue($schedule, 'setting.coopStage.name') === 'Multiple Sites',
        ];
    }

    private static function parseSalmonStage(string $stageName, bool $isBigRun): ?int
    {
        if (!$isBigRun) {
            return SalmonMap3::find()
                ->andWhere(['name' => $stageName])
                ->limit(1)
                ->one()
                ?->id;
        }

        return Map3::find()
            ->andWhere(['name' => $stageName])
            ->limit(1)
            ->one()
            ?->id;
    }

    private static function parseSalmonKing(mixed $kingName): ?SalmonKing3
    {
        if (!is_string($kingName)) {
            return null;
        }

        return SalmonKing3::find()
            ->andWhere(['name' => $kingName])
            ->limit(1)
            ->one();
    }

    /**
     * @return SalmonWeapon3|SalmonRandom3|null
     */
    private static function parseSalmonWeapon(array $info): ?ActiveRecord
    {
        // the JSON does not contain ID value. Let's find by English name

        $name = ArrayHelper::getValue($info, 'name');
        if (!is_string($name)) {
            return null;
        }

        if ($name === 'Random') {
            return SalmonRandom3::find()
                ->andWhere([
                    'key' => match (ArrayHelper::getValue($info, '__splatoon3ink_id')) {
                        '6e17fbe20efecca9', '747937841598fff7', '9d04354f179ebf6f', 'edcfecb7e8acd1a7' => 'random_rare',
                        default => 'random',
                    },
                ])
                ->limit(1)
                ->one();
        }

        return SalmonWeapon3::find()
            ->andWhere(['name' => $name])
            ->limit(1)
            ->one();
    }
}
