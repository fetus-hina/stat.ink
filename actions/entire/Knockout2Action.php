<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use Yii;
use app\models\Knockout2;
use app\models\RankGroup2;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction as BaseAction;

class Knockout2Action extends BaseAction
{
    public function run()
    {
        $query = Knockout2::find()
            ->asArray()
            ->from('knockout2')
            ->innerJoinWith(['rule', 'map'], false)
            ->groupBy(['rule_id', 'map_id'])
            ->select([
                'rule' => 'MAX(rule2.key)',
                'map' => 'MAX(map2.key)',
                'battles' => 'SUM([[battles]])',
                'knockouts' => 'SUM([[knockouts]])',
                'avg_game_time' => 'SUM(avg_game_time * battles) / SUM(battles)',
                'avg_knockout_time' => 'SUM(avg_knockout_time * knockouts) / SUM(knockouts)',
            ]);

        $data = ArrayHelper::map(
            $query->all(),
            'rule',
            function (array $row) : array {
                return $row;
            },
            'map'
        );

        return $this->controller->render('knockout2.php', [
            'data' => $data,
        ]);
    }
}
