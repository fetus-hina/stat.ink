<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal\schedule;

use DateTime;
use Yii;
use app\models\BigrunMap3;
use app\models\Map3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use app\models\SalmonSchedule3;
use app\models\SalmonScheduleWeapon3;
use yii\helpers\ArrayHelper;

use function array_merge;
use function strtotime;

use const SORT_ASC;

trait Salmon3
{
    protected function getSalmon3(): array
    {
        return array_merge(
            $this->getSalmon3Impl(isEggstraWork: false),
            $this->getSalmon3Impl(isEggstraWork: true),
        );
    }

    private function getSalmon3Impl(bool $isEggstraWork): array
    {
        $key = $isEggstraWork ? 'salmon_eggstra' : 'salmon';
        return [
            $key => [
                'key' => $key,
                'game' => 'splatoon3',
                'name' => $isEggstraWork
                    ? Yii::t('app-salmon3', 'Eggstra Work')
                    : Yii::t('app-salmon3', 'Salmon Run'),
                'image' => null,
                'source' => 's3ink',
                'schedules' => ArrayHelper::getColumn(
                    SalmonSchedule3::find()
                        ->with([
                            'bigMap',
                            'king',
                            'map',
                            'salmonScheduleWeapon3s',
                            'salmonScheduleWeapon3s.random',
                            'salmonScheduleWeapon3s.weapon',
                        ])
                        ->andWhere(['is_eggstra_work' => $isEggstraWork])
                        ->andWhere(['>', 'end_at', $this->now->format(DateTime::ATOM)])
                        ->orderBy([
                            'end_at' => SORT_ASC,
                        ])
                        ->limit(10)
                        ->all(),
                    fn (SalmonSchedule3 $sc): array => [
                        'time' => [
                            strtotime($sc->start_at),
                            strtotime($sc->end_at),
                        ],
                        'maps' => [
                            $this->salmonMap3($sc->map ?? $sc->bigMap),
                        ],
                        'king' => $this->salmonKing3($sc->king),
                        'weapons' => ArrayHelper::getColumn(
                            ArrayHelper::sort(
                                $sc->salmonScheduleWeapon3s,
                                fn (SalmonScheduleWeapon3 $a, SalmonScheduleWeapon3 $b): int => $a->id <=> $b->id,
                            ),
                            function (SalmonScheduleWeapon3 $info): array {
                                $w = $info->weapon ?: $info->random;
                                return [
                                    'key' => $w->key,
                                    'name' => Yii::t('app-weapon3', $w->name),
                                    'icon' => null,
                                ];
                            },
                        ),
                        'is_big_run' => $sc->map === null,
                    ],
                ),
            ],
        ];
    }

    private function salmonMap3(SalmonMap3|Map3|BigrunMap3|null $info): ?array
    {
        if (!$info) {
            return null;
        }

        return [
            'key' => $info->key,
            'name' => Yii::t('app-map3', $info->name),
            'image' => null,
        ];
    }

    private function salmonKing3(SalmonKing3|null $king): ?array
    {
        if (!$king) {
            return null;
        }

        return [
            'key' => $king->key,
            'name' => Yii::t('app-salmon-boss3', $king->name),
            'image' => null,
        ];
    }
}
