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
use app\models\User;
use app\models\DeathReason;

class UserStatCauseOfDeathAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        return $this->controller->render('user-stat-cause-of-death.tpl', [
            'user' => $user,
            'list' => $this->getList($user),
        ]);
    }

    public function getList(User $user)
    {
        $query = (new \yii\db\Query())
            ->select([
                'reason_id' => '{{death_reason}}.[[id]]',
                'count' => 'SUM({{battle_death_reason}}.[[count]])',
            ])
            ->from('battle')
            ->innerJoin('battle_death_reason', '{{battle}}.[[id]] = {{battle_death_reason}}.[[battle_id]]')
            ->innerJoin('death_reason', '{{battle_death_reason}}.[[reason_id]] = {{death_reason}}.[[id]]')
            ->andWhere(['{{battle}}.[[user_id]]' => $user->id])
            ->groupBy('{{death_reason}}.[[id]]');
        $ret = array_map(
            function ($row) {
                $o = DeathReason::findOne(['id' => $row['reason_id']]);
                return (object)[
                    'name' => $o->getTranslatedName(),
                    'count' => (int)$row['count'],
                ];
            },
            $query->createCommand()->queryAll()
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
