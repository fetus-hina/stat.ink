<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\internal;

use Yii;
use app\assets\MapImageAsset;
use app\components\helpers\Battle as BattleHelper;
use app\models\Mode2;
use app\models\Map2;
use app\models\PeriodMap;
use app\models\UserWeapon;
use app\models\Weapon2;
use app\models\WeaponType2;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction;

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
        $info = function (array $periodMaps) : array {
            if (!$periodMaps) {
                return [];
            }
            return [
                'rule' => [
                    'key' => $periodMaps[0]->rule->key,
                    'name' => Yii::t('app-rule', $periodMaps[0]->rule->name),
                ],
                'maps' => array_map(
                    function (PeriodMap $pm) : string {
                        return $pm->map->key;
                    },
                    $periodMaps
                ),
            ];
        };
        $info2 = function (array $keys) : array {
            return [
                'rule' => [
                    'key' => 'nawabari',
                    'name' => Yii::t('app-rule2', 'Turf War'),
                ],
                'maps' => $keys,
            ];
        };
        $now = microtime(true);
        $period = BattleHelper::calcPeriod2((int)$now);
        $range = BattleHelper::periodToRange2($period);
        return [
            'period' => [
                'id' => $period,
                'next' => max($range[1] - $now, 0), // in sec
            ],
            'fest'    => $now < 1500112800
                ? $info2(['gangaze', 'ama'])
                : $info2(['combu', 'tachiuo']),
            'regular' => false, // $info(PeriodMap::findCurrentRegular()->all()),
            'gachi'   => false, // $info(PeriodMap::findCurrentGachi()->all()),
        ];
    }

    public function getRules()
    {
        $ret = [];
        foreach (Mode2::find()->with('rules')->asArray()->all() as $mode) {
            $ret[$mode['key']] = (function (array $rules) : array {
                $tmp = [];
                foreach ($rules as $rule) {
                    $tmp[$rule['key']] = [
                        'name' => Yii::t('app-rule2', $rule['name']),
                    ];
                }
                uasort($tmp, function ($a, $b) {
                    return strcasecmp($a['name'], $b['name']);
                });
                return $tmp;
            })($mode['rules']);
        }
        return $ret;
    }

    public function getMaps()
    {
        $assetManager = Yii::$app->assetManager;
        $asset = $assetManager->getBundle(MapImageAsset::class);

        $ret = [];
        foreach (Map2::find()->asArray()->all() as $map) {
            $ret[$map['key']] = [
                'name'      => Yii::t('app-map2', $map['name']),
                'shortName' => Yii::t('app-map2', $map['short_name']),
                'image'     => Url::to($assetManager->getAssetUrl($asset, "daytime/{$map['key']}.jpg"), true),
            ];
        }
        uasort($ret, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });
        return $ret;
    }

    public function getWeapons()
    {
        $ret = [];
        foreach (WeaponType2::find()->orderBy('[[id]]')->asArray()->all() as $type) {
            $ret[] = [
                'name' => Yii::t('app-weapon2', $type['name']),
                'list' => (function (array $type) : array {
                    $tmp = [];
                    foreach (Weapon2::find()->andWhere(['type_id' => $type['id']])->asArray()->all() as $_) {
                        $tmp[$_['key']] = [
                            'name' => Yii::t('app-weapon2', $_['name']),
                        ];
                    }
                    uasort($tmp, function ($a, $b) {
                        return strcasecmp($a['name'], $b['name']);
                    });
                    return $tmp;
                })($type),
            ];
        }
        return $ret;
    }

    public function getFavoriteWeapons()
    {
        $list = array_map(
            function (array $w) : array {
                return [
                    'key' => $w['key'],
                    'name' => Yii::t('app-weapon2', $w['name']),
                ];
            },
            Weapon2::find()
                ->andWhere(['key' => [
                    'sshooter_collabo',
                    'splatroller',
                    'splatcharger',
                    'manueuver',
                ]])
                ->asArray()
                ->all()
        );
        usort($list, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });
        return $list;
        // if (!$user = Yii::$app->user->identity) {
        //     return [];
        // }
        // $list = $user->getUserWeapons()
        //     ->with('weapon')
        //     ->orderBy('[[count]] DESC')
        //     ->limit(10)
        //     ->asArray()
        //     ->all();
        // return array_map(function (array $uw) : array {
        //     return [
        //         'key' => $uw['weapon']['key'],
        //         'name' => Yii::t('app-weapon', $uw['weapon']['name']),
        //     ];
        // }, $list);
    }
}
