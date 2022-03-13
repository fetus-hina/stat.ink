<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\stage;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\GameMode;
use app\models\Map;
use app\models\PeriodMap;
use app\models\Rule;
use app\models\StatWeaponMapTrend;
use stdClass;
use yii\base\DynamicModel;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class MapAction extends BaseAction
{
    public $map;

    public function init()
    {
        parent::init();
        $this->prepare();
    }

    private function prepare()
    {
        $req = Yii::$app->request;

        $model = DynamicModel::validateData(['map' => $req->get('map')], [
            [['map'], 'required'],
            [['map'], 'exist',
                'targetClass' => Map::class,
                'targetAttribute' => 'key',
            ],
        ]);

        if ($model->hasErrors()) {
            self::http404();
        }

        $this->map = Map::findOne(['key' => $model->map]);
    }

    public function run()
    {
        $maps = [];
        foreach (Map::find()->asArray()->all() as $_) {
            $maps[$_['key']] = Yii::t('app-map', $_['name']);
        }
        uasort($maps, 'strnatcasecmp');

        return $this->controller->render('map', [
            'map' => $this->map,
            'data' => $this->buildData(),
            'maps' => $maps,
        ]);
    }

    private function buildData(): array
    {
        // {{{
        $rules = [];
        foreach (GameMode::find()->orderBy('id ASC')->all() as $mode) {
            $tmp = array_map(
                function (Rule $rule): stdClass {
                    $endAt = null;
                    $histories = array_map(
                        function (PeriodMap $period) use (&$endAt): stdClass {
                            $times = BattleHelper::periodToRange($period->period);
                            $interval = $endAt === null ? null : $times[0] - $endAt;
                            $endAt = $times[1];
                            return (object)[
                                'start' => $times[0],
                                'end' => $times[1],
                                'interval' => $interval,
                            ];
                        },
                        $this->map->getPeriodMaps()
                            ->andWhere(['rule_id' => $rule->id])
                            ->orderBy('period ASC')
                            ->all()
                    );
                    return (object)[
                        'rule' => $rule,
                        'history' => array_slice(
                            array_reverse($histories),
                            0,
                            5
                        ),
                        'trends' => StatWeaponMapTrend::find()
                            ->with('weapon')
                            ->where([
                                'rule_id' => $rule->id,
                                'map_id' => $this->map->id,
                            ])
                            ->orderBy('[[battles]] DESC')
                            ->limit(5)
                            ->all(),
                        'trendTotalBattles' => StatWeaponMapTrend::find()
                            ->where([
                                'rule_id' => $rule->id,
                                'map_id' => $this->map->id,
                            ])
                            ->sum('battles'),
                    ];
                },
                $mode->rules
            );
            usort($tmp, fn ($a, $b) => strnatcasecmp(
                Yii::t('app-rule', $a->rule->name),
                Yii::t('app-rule', $b->rule->name)
            ));
            $rules = array_merge($rules, $tmp);
        }
        return $rules;
        // }}}
    }

    private static function http404()
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
