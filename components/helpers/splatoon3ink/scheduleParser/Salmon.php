<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\splatoon3ink\scheduleParser;

use DateTimeImmutable;
use DateTimeZone;
use app\models\Map3;
use app\models\SalmonMap3;
use app\models\SalmonRandom3;
use app\models\SalmonWeapon3;
use app\models\api\v3\postBattle\TypeHelperTrait;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

trait Salmon
{
    use TypeHelperTrait;

    protected static function salmon(array $nodes, bool $isBigRun): array
    {
        return \array_values(
            \array_map(
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
            'weapons' => \array_map(
                fn (array $info): ?ActiveRecord => self::parseSalmonWeapon($info),
                ArrayHelper::getValue($schedule, 'setting.weapons'),
            ),
        ];
    }

    private static function parseTimestamp(string $ts): int
    {
        $obj = new DateTimeImmutable($ts, new DateTimeZone('Etc/UTC'));
        return $obj->getTimestamp();
    }

    private static function parseSalmonStage(string $stageName, bool $isBigRun): int
    {
        if (!$isBigRun) {
            return SalmonMap3::find()
                ->andWhere(['name' => $stageName])
                ->limit(1)
                ->one()
                ->id;
        }

        return Map3::find()
            ->andWhere(['name' => $stageName])
            ->limit(1)
            ->one()
            ->id;
    }

    /**
     * @return SalmonWeapon3|SalmonRandom3|null
     */
    private static function parseSalmonWeapon(array $info): ?ActiveRecord
    {
        // the JSON does not contain ID value. Let's find by English name

        $name = ArrayHelper::getValue($info, 'name');
        if ($name === 'Random') {
            // TODO: rare random (use image url)
            return SalmonRandom3::find()
                ->andWhere(['key' => 'random'])
                ->limit(1)
                ->one();
        }

        return SalmonWeapon3::find()
            ->andWhere(['name' => $name])
            ->limit(1)
            ->one();
    }
}
