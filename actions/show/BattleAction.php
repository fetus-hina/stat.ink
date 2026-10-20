<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\show;

use Override;
use Yii;
use app\models\Battle;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ViewAction as BaseAction;

class BattleAction extends BaseAction
{
    #[Override]
    public function run(): string|Response
    {
        $request = Yii::$app->getRequest();

        $battle = Battle::findOne([
            'id' => $request->get('battle'),
        ]);
        if (!$battle) {
            throw new NotFoundHttpException(
                Yii::t('app-show', 'Could not find specified battle.'),
            );
        }

        if ($battle->user->screen_name !== $request->get('screen_name')) {
            return $this->controller->redirect([
                'show/battle',
                'screen_name' => $battle->user->screen_name,
                'battle' => $battle->id,
            ]);
        }

        return $this->controller->render('battle', [
            'battle' => $battle,
        ]);
    }
}
