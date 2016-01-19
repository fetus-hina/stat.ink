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
use app\models\BattleFilterForm;
use app\models\DeathReason;
use app\models\User;

class UserStatCauseOfDeathAction extends BaseAction
{
    use UserStatFilterTrait;

    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        $filter = new BattleFilterForm();
        $filter->load($_GET);
        $filter->screen_name = $user->screen_name;
        $filter->validate();

        return $this->controller->render('user-stat-cause-of-death.tpl', [
            'user' => $user,
            'list' => $this->getList($user, $filter),
            'filter' => $filter,
        ]);
    }

    public function getList(User $user, BattleFilterForm $filter)
    {
        $query = (new \yii\db\Query())
            ->select([
                'reason_id' => '{{death_reason}}.[[id]]',
                'count' => 'SUM({{battle_death_reason}}.[[count]])',
            ])
            ->from('battle')
            ->innerJoin('battle_death_reason', '{{battle}}.[[id]] = {{battle_death_reason}}.[[battle_id]]')
            ->innerJoin('death_reason', '{{battle_death_reason}}.[[reason_id]] = {{death_reason}}.[[id]]')
            ->leftJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->leftJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
            ->leftJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->leftJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
            ->leftJoin('weapon', '{{battle}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->leftJoin('weapon_type', '{{weapon}}.[[type_id]] = {{weapon_type}}.[[id]]')
            ->leftJoin('subweapon', '{{weapon}}.[[subweapon_id]] = {{subweapon}}.[[id]]')
            ->leftJoin('special', '{{weapon}}.[[special_id]] = {{special}}.[[id]]')
            ->andWhere(['{{battle}}.[[user_id]]' => $user->id])
            ->groupBy('{{death_reason}}.[[id]]');

        if ($filter && !$filter->hasErrors()) {
            $this->filter($query, $filter);
        }

        $list = $query->createCommand()->queryAll();

        // 必要な死因名の一覧を作る
        $deathReasons = [];
        array_map(
            function ($o) use (&$deathReasons) {
                $deathReasons[$o->id] = $o->getTranslatedName();
            },
            DeathReason::findAll(['id' => array_map(function ($row) {
                return $row['reason_id'];
            }, $list)])
        );

        $ret = array_map(
            function ($row) use ($deathReasons) {
                return (object)[
                    'name' => @$deathReasons[$row['reason_id']] ?: '?',
                    'count' => (int)$row['count'],
                ];
            },
            $list
        );
        usort($ret, function ($a, $b) {
            if ($a->count !== $b->count) {
                return $b->count - $a->count;
            }
            return strcasecmp($a->name, $b->name);
        });
        return $ret;
    }
}
