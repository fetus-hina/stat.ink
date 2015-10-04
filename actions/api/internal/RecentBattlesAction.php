<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\internal;

use DateTimeZone;
use Yii;
use app\models\Battle;
use app\models\User;

class RecentBattlesAction extends BaseStatAction
{
    protected function makeData()
    {
        $timeNow    = isset($_SERVER['REQUEST_TIME']) ? (int)$_SERVER['REQUEST_TIME'] : time();
        $timeStart  = $timeNow - 30 * 86400;
        $query = $this->user->getBattles()
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
