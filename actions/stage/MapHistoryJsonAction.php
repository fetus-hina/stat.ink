<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\stage;

use Yii;
use app\models\Map;
use app\models\PeriodMap;
use app\models\Rule;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class MapHistoryJsonAction extends BaseAction
{
    public $map;
    public $rule;

    public function init()
    {
        parent::init();

        Yii::$app->response->format = 'json';

        $req = Yii::$app->request;
        $this->map = Map::findOne(['key' => $req->get('map')]);
        $this->rule = Rule::findOne(['key' => $req->get('rule')]);
        if (!$this->map || !$this->rule) {
            self::http404();
            return;
        }
    }

    public function run()
    {
        $query = (new Query())
            ->select([
                'date' => 'CAST(period_to_timestamp([[period]]) as DATE)',
                'count' => 'COUNT(*)',
            ])
            ->from(PeriodMap::tableName())
            ->where([
                'map_id' => $this->map->id,
                'rule_id' => $this->rule->id,
            ])
            ->groupBy('CAST(period_to_timestamp([[period]]) as DATE)')
            ->orderBy('date ASC');
        $ret = [];
        foreach ($query->all() as $row) {
            $ret[$row['date']] = (int)$row['count'];
        }
        return $ret;
    }

    private static function http404()
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
