<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\site;

use yii\web\ViewAction as BaseAction;
use app\models\Battle;

class UsersAction extends BaseAction
{
    public function run()
    {
        $subQuery = (new \yii\db\Query())
            ->select(['id' => 'MAX({{battle}}.[[id]])'])
            ->from('battle')
            ->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d H:i:sO', time() - 60 * 86400)])
            ->groupBy('{{battle}}.[[user_id]]');

        $battles = Battle::find()
            ->andWhere(['in', '{{battle}}.[[id]]', $subQuery])
            ->with(['user', 'rule', 'map', 'battleImageResult'])
            ->limit(500)
            ->each();

        return $this->controller->render('users.tpl', [
            'battles' => $battles,
        ]);
    }
}
