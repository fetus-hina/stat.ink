<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\entire;

use Yii;
use app\models\Knockout2;
use app\models\Knockout2FilterForm;
use app\models\RankGroup2;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction as BaseAction;

class Knockout2Action extends BaseAction
{
    public function run()
    {
        $form = Yii::createObject(Knockout2FilterForm::class);

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
                'avg_game_time' => 'SUM(avg_game_time * battles) / NULLIF(SUM(battles), 0)',
                'avg_knockout_time' => 'SUM(avg_knockout_time * knockouts) / NULLIF(SUM(knockouts), 0)',
            ]);

        if ($form->load($_GET) && $form->validate()) {
            if ($form->lobby != '') {
                $query
                    ->innerJoinWith('lobby', false)
                    ->andWhere([
                        '{{lobby2}}.[[key]]' => (function (string $lobby) {
                            switch ($lobby) {
                                case 'squad':
                                    return ['squad_2', 'squad_4'];

                                case 'standard':
                                case 'squad_2':
                                case 'squad_4':
                                    return $lobby;

                                default:
                                    return 'ERROR';
                            }
                        })($form->lobby),
                    ]);
            }
            if ($form->rank != '') {
                $query
                    ->innerJoinWith('rank.group', false)
                    ->andWhere(['{{rank_group2}}.[[key]]' => $form->rank]);
            }
        }

        $data = ArrayHelper::map(
            $query->all(),
            'rule',
            function (array $row): array {
                return $row;
            },
            'map'
        );

        return $this->controller->render('knockout2.php', [
            'data' => $data,
            'form' => $form,
        ]);
    }
}
