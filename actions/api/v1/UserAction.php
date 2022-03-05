<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\v1;

use Yii;
use app\models\Battle;
use app\models\User;
use yii\db\Query;
use yii\web\ViewAction as BaseAction;

class UserAction extends BaseAction
{
    public function run()
    {
        Yii::$app->response->format = 'json';
        $request = Yii::$app->getRequest();
        $screenName = $request->get('screen_name');

        $user = null;
        if ($screenName != '') {
            // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
            if (is_scalar($screenName) && ($user = User::findOne(['screen_name' => $screenName]))) {
                // ok
            } else {
                return [
                    'error' => [
                        'screen_name' => ['not found'],
                    ],
                ];
            }
        }

        $now = @$_SERVER['REQUEST_TIME'] ?: time();
        $subQuery = (new Query())
            ->select(['id' => 'MAX({{battle}}.[[id]])'])
            ->from('battle')
            ->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d H:i:sO', $now - 60 * 86400)])
            ->groupBy('{{battle}}.[[user_id]]');
        if ($user) {
            $subQuery->andWhere(['{{battle}}.[[user_id]]' => $user->id]);
        }

        $battles = Battle::find()
            ->andWhere(['in', '{{battle}}.[[id]]', $subQuery])
            ->with([
                'user',
                'user.userStat',
            ])
            ->limit(500)
            ->orderBy('{{battle}}.[[id]] DESC');

        $ret = [];
        foreach ($battles->each() as $model) {
            $json = $model->user->toJsonArray();
            $json['latest_battle'] = null;
            $ret[] = $json;
        }

        if ($user) {
            return count($ret) >= 1 ? array_shift($ret) : null;
        }
        return $ret;
    }
}
