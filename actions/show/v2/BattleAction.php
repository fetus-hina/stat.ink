<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v2;

use Yii;
use app\models\Battle2;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

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
            ->with(ArrayHelper::toFlatten(array_map(
                fn (string $base): array => [
                        "{$base}",
                        "{$base}.primaryAbility",
                        "{$base}.gear",
                        "{$base}.secondaries.ability",
                    ],
                [
                    'headgear',
                    'clothing',
                    'shoes',
                ],
            )))
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
