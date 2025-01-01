<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\stage;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Map;
use app\models\PeriodMap;
use app\models\Rule;
use app\models\StatWeaponMapTrend;
use stdClass;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

use function array_map;
use function array_reverse;
use function uasort;

class MapDetailAction extends BaseAction
{
    public $map;
    public $rule;

    public function init()
    {
        parent::init();

        $req = Yii::$app->request;
        $this->map = Map::findOne(['key' => $req->get('map')]);
        $this->rule = Rule::findOne(['key' => $req->get('rule')]);
        if (!$this->map || !$this->rule) {
            static::http404();
            return;
        }
    }

    public function run()
    {
        return $this->controller->render('map-detail', [
            'map' => $this->map,
            'maps' => $this->getMaps(),
            'rule' => $this->rule,
            'rules' => $this->getRules(),
            'history' => array_reverse($this->getHistory()),
            'weapons' => $this->getWeaponData(),
        ]);
    }

    private static function http404()
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    private function getMaps(): array
    {
        $ret = [];
        foreach (Map::find()->asArray()->all() as $_) {
            $ret[$_['key']] = Yii::t('app-map', $_['name']);
        }
        uasort($ret, 'strnatcasecmp');
        return $ret;
    }

    private function getRules(): array
    {
        $ret = [];
        foreach (Rule::find()->asArray()->orderBy('id')->all() as $_) {
            $ret[$_['key']] = Yii::t('app-rule', $_['name']);
        }
        return $ret;
    }

    private function getHistory(): array
    {
        $endAt = null;
        return array_map(
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
            $this->map
                ->getPeriodMaps()
                ->andWhere(['rule_id' => $this->rule->id])
                ->orderBy('period ASC')
                ->all(),
        );
    }

    private function getWeaponData(): array
    {
        return StatWeaponMapTrend::find()
            ->with('weapon')
            ->andWhere([
                'rule_id' => $this->rule->id,
                'map_id' => $this->map->id,
            ])
            ->orderBy('[[battles]] DESC')
            ->all();
    }
}
