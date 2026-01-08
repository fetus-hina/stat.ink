<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\internal;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\GameMode;
use app\models\Map;
use app\models\PeriodMap;
use app\models\Weapon;
use app\models\WeaponType;
use statink\yii2\stages\spl1\Spl1Stage;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction;

use function array_map;
use function max;
use function microtime;
use function strcasecmp;
use function uasort;

class CurrentDataAction extends ViewAction
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
        $info = function (array $periodMaps): array {
            if (!$periodMaps) {
                return [];
            }
            return [
                'rule' => [
                    'key' => $periodMaps[0]->rule->key,
                    'name' => Yii::t('app-rule', $periodMaps[0]->rule->name),
                ],
                'maps' => array_map(
                    fn (PeriodMap $pm): string => $pm->map->key,
                    $periodMaps,
                ),
            ];
        };

        $now = microtime(true);
        $period = BattleHelper::calcPeriod((int)$now);
        $range = BattleHelper::periodToRange($period);
        return [
            'period' => [
                'id' => $period,
                'next' => max($range[1] - $now, 0), // in sec
            ],
            'fest' => false,
            'regular' => $info(PeriodMap::findCurrentRegular()->all()),
            'gachi' => $info(PeriodMap::findCurrentGachi()->all()),
        ];
    }

    public function getRules()
    {
        $ret = [];
        foreach (GameMode::find()->with('rules')->asArray()->all() as $mode) {
            $ret[$mode['key']] = (function (array $rules): array {
                $tmp = [];
                foreach ($rules as $rule) {
                    $tmp[$rule['key']] = [
                        'name' => Yii::t('app-rule', $rule['name']),
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
        foreach (Map::find()->asArray()->all() as $map) {
            $ret[$map['key']] = [
                'name' => Yii::t('app-map', $map['name']),
                'shortName' => Yii::t('app-map', $map['short_name']),
                'image' => Url::to(Spl1Stage::url('daytime', $map['key']), true),
            ];
        }
        uasort($ret, fn ($a, $b) => strcasecmp($a['name'], $b['name']));
        return $ret;
    }

    public function getWeapons()
    {
        $ret = [];
        foreach (WeaponType::find()->orderBy('[[id]]')->asArray()->all() as $type) {
            $ret[] = [
                'name' => Yii::t('app-weapon', $type['name']),
                'list' => (function (array $type): array {
                    $tmp = [];
                    foreach (Weapon::find()->andWhere(['type_id' => $type['id']])->asArray()->all() as $_) {
                        $tmp[$_['key']] = [
                            'name' => Yii::t('app-weapon', $_['name']),
                        ];
                    }
                    uasort($tmp, fn ($a, $b) => strcasecmp($a['name'], $b['name']));
                    return $tmp;
                })($type),
            ];
        }
        return $ret;
    }

    public function getFavoriteWeapons()
    {
        if (!$user = Yii::$app->user->identity) {
            return [];
        }
        $list = $user->getUserWeapons()
            ->with('weapon')
            ->orderBy('[[count]] DESC')
            ->limit(10)
            ->asArray()
            ->all();
        return array_map(
            fn (array $uw): array => [
                'key' => $uw['weapon']['key'],
                'name' => Yii::t('app-weapon', $uw['weapon']['name']),
            ],
            $list,
        );
    }
}
