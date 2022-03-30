<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Map2;
use app\models\Mode2;
use app\models\UserWeapon2;
use app\models\Weapon2;
use app\models\WeaponCategory2;
use app\models\WeaponType2;
use statink\yii2\stages\spl2\Spl2Stage;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction;

use const SORT_ASC;
use const SORT_DESC;

class CurrentData2Action extends ViewAction
{
    public function init()
    {
        parent::init();
        Yii::$app->response->format = YII_ENV_DEV ? 'json' : 'compact-json';
        if (Yii::$app->user->isGuest) {
            throw new BadRequestHttpException();
        }
    }

    public function run()
    {
        return [
            'current' => $this->getCurrentInfo(),
            'rules' => $this->getRules(),
            'maps' => $this->getMaps(),
            'weapons' => $this->getWeapons(),
            'favWeapons' => $this->getFavoriteWeapons(),
        ];
    }

    public function getCurrentInfo()
    {
        // $info = function (array $periodMaps): array {
        //     if (!$periodMaps) {
        //         return [];
        //     }
        //     return [
        //         'rule' => [
        //             'key' => $periodMaps[0]->rule->key,
        //             'name' => Yii::t('app-rule', $periodMaps[0]->rule->name),
        //         ],
        //         'maps' => array_map(
        //             fn (PeriodMap $pm): string => $pm->map->key,
        //             $periodMaps
        //         ),
        //     ];
        // };
        // $info2 = fn (array $keys): array => [
        //     'rule' => [
        //         'key' => 'nawabari',
        //         'name' => Yii::t('app-rule2', 'Turf War'),
        //     ],
        //     'maps' => $keys,
        // ];
        $now = microtime(true);
        $period = BattleHelper::calcPeriod2((int)$now);
        $range = BattleHelper::periodToRange2($period);
        $fest = false;
        return [
            'period' => [
                'id' => $period,
                'next' => max($range[1] - $now, 0), // in sec
            ],
            'fest'    => $fest,
            'regular' => false, // $info(PeriodMap::findCurrentRegular()->all()),
            'gachi'   => false, // $info(PeriodMap::findCurrentGachi()->all()),
        ];
    }

    public function getRules()
    {
        $ret = [];
        foreach (Mode2::find()->with('rules')->asArray()->all() as $mode) {
            $ret[$mode['key']] = (function (array $rules): array {
                $tmp = [];
                foreach ($rules as $rule) {
                    $tmp[$rule['key']] = [
                        'name' => Yii::t('app-rule2', $rule['name']),
                    ];
                }
                uasort($tmp, fn ($a, $b) => strcasecmp($a['name'], $b['name']));
                return $tmp;
            })($mode['rules']);
        }
        return $ret;
    }

    public function getMaps()
    {
        $ret = [];
        foreach (Map2::find()->asArray()->all() as $map) {
            $ret[$map['key']] = [
                'name' => Yii::t('app-map2', $map['name']),
                'shortName' => Yii::t('app-map2', $map['short_name']),
                'image' => Url::to(Spl2Stage::url('daytime', $map['key']), true),
            ];
        }
        uasort($ret, fn ($a, $b) => strcasecmp($a['name'], $b['name']));
        return $ret;
    }

    public function getWeapons(): array
    {
        $ret = [];
        foreach (WeaponCategory2::find()->orderBy(['id' => SORT_ASC])->all() as $category) {
            $q = WeaponType2::find()
                ->andWhere(['category_id' => $category->id])
                ->orderBy(['id' => SORT_ASC]);
            foreach ($q->all() as $type) {
                $weapons = Weapon2::find()
                    ->andWhere(['type_id' => $type->id])
                    ->asArray()
                    ->all();
                if ($weapons) {
                    $ret[] = [
                        'name' => $category->name === $type->name
                            ? Yii::t('app-weapon2', $type->name)
                            : sprintf(
                                '%s » %s',
                                Yii::t('app-weapon2', $category->name),
                                Yii::t('app-weapon2', $type->name)
                            ),
                        'list' => (function () use ($weapons): array {
                            $tmp = ArrayHelper::map(
                                $weapons,
                                'key',
                                fn (array $weapon): array => [
                                    'name' => Yii::t('app-weapon2', $weapon['name']),
                                ]
                            );
                            uasort($tmp, fn (array $a, array $b) => strcasecmp($a['name'], $b['name']));
                            return $tmp;
                        })(),
                    ];
                }
            }
        }
        return $ret;
    }

    public function getFavoriteWeapons(): array
    {
        if (!$user = Yii::$app->user->identity) {
            return [];
        }

        $fmt = Yii::$app->formatter;
        return array_map(
            fn (UserWeapon2 $row): array => [
                'key' => $row->weapon->key,
                'name' => sprintf(
                    '%s (%s)',
                    Yii::t('app-weapon2', $row->weapon->name),
                    $fmt->asInteger($row->battles),
                ),
            ],
            UserWeapon2::find()
                ->with('weapon')
                ->andWhere(['user_id' => $user->id])
                ->andWhere(['>', 'battles', 0])
                ->orderBy(['battles' => SORT_DESC])
                ->limit(10)
                ->all(),
        );
    }
}
