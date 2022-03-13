<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\show;

use Yii;
use app\components\helpers\T;
use app\models\Battle;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class BattleAction extends Action
{
    /**
     * @return Response|string
     */
    public function run()
    {
        $request = Yii::$app->getRequest();

        $battle = Battle::findOne([
            'id' => $request->get('battle'),
        ]);
        if (!$battle || !$battle->user) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Could not find specified battle.')
            );
        }

        if ($battle->user->screen_name !== $request->get('screen_name')) {
            return T::webController($this->controller)
                ->redirect(['show/battle',
                    'screen_name' => $battle->user->screen_name,
                    'battle' => $battle->id,
                ]);
        }

        return $this->controller->render('battle', [
            'battle' => $battle,
        ]);
    }
}
