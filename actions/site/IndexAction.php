<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\site;

use yii\web\ViewAction as BaseAction;
use app\models\Battle;
use app\models\BattleImageType;

class IndexAction extends BaseAction
{
    public function run()
    {
        $active = Battle::find()
            ->hasResultImage()
            ->andWhere(['in', '{{battle}}.[[id]]', $this->getActiveUserBattleIdList()])
            ->with(['user', 'rule', 'map', 'battleImageResult'])
            ->all();

        return $this->controller->render('index.tpl', [
            'active' => $active,
        ]);
    }

    private function getActiveUserBattleIdList()
    {
        $query = (new \yii\db\Query())
            ->select(['id' => 'MAX({{battle}}.[[id]])'])
            ->from('battle')
            ->innerJoin(
                'battle_image',
                '{{battle}}.[[id]] = {{battle_image}}.[[battle_id]] AND ' .
                '{{battle_image}}.[[type_id]] = :image_type_result',
                [':image_type_result' => BattleImageType::ID_RESULT]
            )
            ->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d H:i:sO', time() - 7 * 86400)])
            ->groupBy('{{battle}}.[[user_id]]')
            ->orderBy('MAX({{battle}}.[[id]]) DESC')
            ->limit(12);
        return array_map(
            function ($row) {
                return (int)$row['id'];
            },
            $query->createCommand()->queryAll()
        );
    }
}
