<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\show\v2;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\Battle2;
use app\models\User;
use app\components\helpers\DateTimeFormatter;

class BattleAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();

        $battle = Battle2::find()
            ->withFreshness()
            ->andWhere(['battle2.id' => $request->get('battle')])
            ->with([
                'myTeamPlayers',
                'myTeamPlayers.rank',
                'hisTeamPlayers',
                'hisTeamPlayers.rank',
            ])
            ->limit(1)
            ->one();
        if (!$battle || !$battle->user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        if ($battle->user->screen_name !== $request->get('screen_name')) {
            return $this->controller->redirect([
                'show-v2/battle',
                'screen_name' => $battle->user->screen_name,
                'battle' => $battle->id,
            ]);
        }

        return $this->controller->render('battle', [
            'battle' => $battle,
        ]);
    }
}
