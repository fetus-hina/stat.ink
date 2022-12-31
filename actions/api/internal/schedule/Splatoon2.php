<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal\schedule;

use DateTime;
use Yii;
use app\assets\GameModeIconsAsset;
use app\assets\Spl2WeaponAsset;
use app\components\helpers\Battle as BattleHelper;
use app\models\Map2;
use app\models\SalmonSchedule2;
use app\models\SalmonWeapon2;
use app\models\Schedule2;
use app\models\ScheduleMode2;
use statink\yii2\stages\spl2\StagesAsset as Stages2Asset;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use const SORT_ASC;

trait Splatoon2
{
    protected function getSplatoon2(): array
    {
        return \array_merge(
            $this->getSplatoon2Battles(),
            [
                'salmon' => $this->getSalmon2(),
            ],
        );
    }

    private function getSplatoon2Battles(): array
    {
        $am = Yii::$app->assetManager;
        return ArrayHelper::map(
            ScheduleMode2::find()->orderBy(['id' => SORT_ASC])->all(),
            'key',
            function (ScheduleMode2 $mode) use ($am): array {
                return [
                    'key' => $mode->key,
                    'game' => 'splatoon2',
                    'name' => Yii::t('app-rule2', $mode->name),
                    'image' => $mode->key === 'regular'
                        ? null
                        : Url::to(
                            $am->getAssetUrl(
                                $am->getBundle(GameModeIconsAsset::class, true),
                                \sprintf('spl2/%s.png', $mode->key)
                            ),
                            true
                        ),
                    'source' => 's2ink',
                    'schedules' => ArrayHelper::getColumn(
                        Schedule2::find()
                            ->andWhere(['mode_id' => $mode->id])
                            ->andWhere(['>=', '{{schedule2}}.[[period]]', $this->currentPeriod])
                            ->orderBy([
                                '{{schedule2}}.[[period]]' => SORT_ASC,
                            ])
                            ->limit(10)
                            ->with([
                                'maps',
                                'rule',
                            ])
                            ->all(),
                        function (Schedule2 $sc) use ($am): array {
                            return [
                                'time' => BattleHelper::periodToRange2((int)$sc->period),
                                'rule' => [
                                    'key' => $sc->rule->key,
                                    'name' => Yii::t('app-rule2', $sc->rule->name),
                                    'short' => Yii::t('app-rule2', $sc->rule->short_name),
                                    'icon' => Url::to(
                                        $am->getAssetUrl(
                                            $am->getBundle(GameModeIconsAsset::class, true),
                                            \sprintf('spl2/%s.png', $sc->rule->key)
                                        ),
                                        true
                                    ),
                                ],
                                'maps' => ArrayHelper::getColumn(
                                    $sc->maps,
                                    function (Map2 $map) use ($am): array {
                                        return [
                                            'key' => $map->key,
                                            'name' => Yii::t('app-map2', $map->name),
                                            'image' => Url::to(
                                                $am->getAssetUrl(
                                                    $am->getBundle(Stages2Asset::class, true),
                                                    \sprintf('daytime/%s.jpg', $map->key)
                                                ),
                                                true
                                            ),
                                        ];
                                    }
                                ),
                            ];
                        }
                    ),
                ];
            }
        );
    }

    private function getSalmon2(): array
    {
        $am = Yii::$app->assetManager;
        return [
            'key' => 'salmon',
            'game' => 'splatoon2',
            'name' => Yii::t('app-salmon2', 'Salmon Run'),
            'image' => Url::to(
                $am->getAssetUrl(
                    $am->getBundle(GameModeIconsAsset::class, true),
                    'spl2/salmon.png',
                ),
                true
            ),
            'source' => 's2ink',
            'schedules' => ArrayHelper::getColumn(
                SalmonSchedule2::find()
                    ->andWhere([
                        '>',
                        '{{salmon_schedule2}}.[[end_at]]',
                        $this->now->format(DateTime::ATOM),
                    ])
                    ->orderBy([
                        '{{salmon_schedule2}}.[[end_at]]' => SORT_ASC,
                    ])
                    ->limit(10)
                    ->with([
                        'map',
                        'weapons.weapon',
                    ])
                    ->all(),
                function (SalmonSchedule2 $sc) use ($am): array {
                    return [
                        'time' => [
                            strtotime($sc->start_at),
                            strtotime($sc->end_at),
                        ],
                        'maps' => [[
                            'key' => $sc->map->key,
                            'name' => Yii::t('app-salmon-map2', $sc->map->name),
                            'image' => Url::to(
                                $am->getAssetUrl(
                                    $am->getBundle(Stages2Asset::class, true),
                                    sprintf('daytime/%s.jpg', $sc->map->key)
                                ),
                                true
                            ),
                        ]],
                        'weapons' => $this->fillSalmon2Weapon(
                            ArrayHelper::getColumn(
                                $sc->weapons,
                                function (SalmonWeapon2 $info) use ($am): array {
                                    $w = $info->weapon;
                                    return [
                                        'key' => $w->key,
                                        'name' => Yii::t('app-weapon2', $w->name),
                                        'icon' => Url::to(
                                            $am->getAssetUrl(
                                                $am->getBundle(Spl2WeaponAsset::class, true),
                                                $w->key . '.png'
                                            ),
                                            true
                                        ),
                                    ];
                                }
                            )
                        ),
                    ];
                },
            ),
        ];
    }

    private function fillSalmon2Weapon(array $list): array
    {
        while (\count($list) < 4) {
            $list[] = [
                'key' => 'random',
                'name' => Yii::t('app-salmon2', 'Random'),
                'icon' => null,
            ];
        }
        return $list;
    }
}
