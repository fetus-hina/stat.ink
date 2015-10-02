<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\internal;

use DateTimeZone;
use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Battle;
use app\models\User;

class RecentBattlesAction extends BaseAction
{
    const FORMAT_VERSION = 1;
    const CACHE_EXPIRES = 600;

    public function run()
    {
        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        $request = Yii::$app->getRequest();
        $screenName = $request->get('screen_name');
        if (!is_scalar($screenName)) {
            return ['error'=>['screen_name'=>['not found']]];
        }
        if (!$user = User::findOne(['screen_name' => $screenName])) {
            return ['error'=>['screen_name'=>['not found']]];
        }
        return $this->makeDataOrLoadCache($user);
    }

    private function makeDataOrLoadCache(User $user)
    {
        $cache = Yii::$app->cache;
        if (!$cache) {
            return $this->makeData($user);
        }
        $key = hash_hmac(
            'sha256',
            http_build_query(['user_id' => $user->id, 'format' => self::FORMAT_VERSION], '', '&'),
            __METHOD__
        );
        $data = $cache->get($key);
        if (is_array($data)) {
            return $data;
        }
        $data = $this->makeData($user);
        $cache->set($key, $data, self::CACHE_EXPIRES);
        return $data;
    }

    private function makeData(User $user)
    {
        $timeNow = (int)$_SERVER['REQUEST_TIME'];
        $timeStart = $timeNow - 30 * 86400;
        $query = $user->getBattles()
            ->orderBy('{{battle}}.[[end_at]] ASC')
            ->andWhere(['>', '{{battle}}.[[end_at]]', gmdate('Y-m-d H:i:sO', $timeStart)])
            ->andWhere(['<=', '{{battle}}.[[end_at]]', gmdate('Y-m-d H:i:sO', $timeNow)])
            ->andWhere(['in', '{{battle}}.[[is_win]]', [true, false]])
            ->with(['rule', 'rule.mode']);
        $ret = [];
        foreach ($query->each() as $battle) {
            $ret[] = [
                'id'        => $battle->id,
                'rule'      => isset($battle->rule) ? $battle->rule->key : null,
                'mode'      => isset($battle->rule->mode) ? $battle->rule->mode->key : null,
                'is_win'    => $battle->is_win,
                'at'        => strtotime($battle->end_at),
            ];
        }
        return [
            'term' => [
                's' => $timeStart,
                'e' => $timeNow,
            ],
            'battles' => $ret,
        ];
    }
}
