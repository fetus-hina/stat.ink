<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\Battle;
use app\models\User;
use app\components\helpers\DateTimeFormatter;

class BattleAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException('指定されたユーザが見つかりません');
        }

        $battle = Battle::findOne([
            'user_id' => $user->id,
            'id' => $request->get('battle'),
        ]);
        if (!$battle) {
            throw new NotFoundHttpExcpetion('指定されたバトルが見つかりません');
        }

        $test = [
            'id' => $battle->id,
            'user' => $battle->user ? $battle->user->toJsonArray() : null,
            'rule' => $battle->rule ? $battle->rule->toJsonArray() : null,
            'map' => $battle->map ? $battle->map->toJsonArray() : null,
            'weapon' => $battle->weapon ? $battle->weapon->toJsonArray() : null,
            'rank' => $battle->rank ? $battle->rank->toJsonArray() : null,
            'level' => $battle->level,
            'result' => $battle->is_win === true ? 'win' : ($battle->is_win === false ? 'lose' : null),
            'rank_in_team' => $battle->rank_in_team,
            'kill' => $battle->kill,
            'death' => $battle->death,
            'agent' => [
                'name' => $battle->agent,
                'version' => $battle->agent_version,
            ],
            'start_at' => $battle->start_at != ''
                ? DateTimeFormatter::unixTimeToJsonArray(strtotime($battle->start_at))
                : null,
            'end_at' => $battle->end_at != ''
                ? DateTimeFormatter::unixTimeToJsonArray(strtotime($battle->end_at))
                : null,
            'register_at' => DateTimeFormatter::unixTimeToJsonArray(strtotime($battle->at)),
        ];
        $json = json_encode($test, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $this->controller->render('battle.tpl', [
            'user' => $user,
            'battle' => $battle,
            'json' => $json,
        ]);
    }
}
