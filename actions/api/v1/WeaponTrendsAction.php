<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\v1;

use Yii;
use app\models\StatWeaponMapTrend;
use app\models\api\v1\WeaponTrendsGetForm as Form;
use yii\web\ViewAction as BaseAction;

use function implode;
use function ini_set;

class WeaponTrendsAction extends BaseAction
{
    public function init()
    {
        parent::init();
        Yii::$app->language = 'en-US';
    }

    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';

        $form = Yii::createObject(Form::class);
        $form->attributes = Yii::$app->getRequest()->get();
        if (!$form->validate()) {
            $response->statusCode = 400;
            return [
                'error' => $form->getErrors(),
            ];
        }

        $query = StatWeaponMapTrend::find()
            ->with(['weapon', 'weapon.subweapon', 'weapon.special'])
            ->where([
                'stat_weapon_map_trend.rule_id' => $form->ruleId,
                'stat_weapon_map_trend.map_id' => $form->mapId,
            ])
            ->orderBy(implode(', ', [
                'stat_weapon_map_trend.battles DESC',
                'stat_weapon_map_trend.weapon_id',
            ]));

        $totalCount = (clone $query)->orderBy(null)->sum('stat_weapon_map_trend.battles');
        $json = [];
        $rank = 0;
        $lastCount = null;
        ini_set('serialize_precision', 3);
        foreach ($query->all() as $i => $row) {
            if ($row->battles !== $lastCount) {
                $rank = $i + 1;
                $lastCount = $row->battles;
            }
            $json[] = [
                'rank' => $rank,
                'use_pct' => $totalCount > 0
                        ? ($row->battles * 100 / $totalCount)
                        : null,
                'weapon' => $row->weapon->toJsonArray(),
            ];
        }

        return $json;
    }
}
