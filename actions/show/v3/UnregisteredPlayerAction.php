<?php

/**
 * @copyright Copyright (C) 2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v3;

use Yii;
use app\models\UnregisteredPlayer3;
use yii\base\Action;
use yii\web\NotFoundHttpException;

final class UnregisteredPlayerAction extends Action
{
    public function run(): string
    {
        $request = Yii::$app->request;
        $splashtag = (string)$request->get('splashtag');

        $player = null;

        if ($splashtag) {
            $player = UnregisteredPlayer3::findBySplashtagString($splashtag);
        }

        if (!$player || !$player->hasSignificantData()) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Could not find player or insufficient data available')
            );
        }

        return $this->controller->render('unregistered-player', [
            'player' => $player,
        ]);
    }
}