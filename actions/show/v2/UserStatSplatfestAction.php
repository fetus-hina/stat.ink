<?php

/**
 * @copyright Copyright (C) 2021-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\show\v2;

use LogicException;
use Yii;
use app\models\Region2;
use app\models\Splatfest2;
use app\models\User;
use app\models\UserStatSplatfest2Form;
use yii\base\Action;
use yii\web\NotFoundHttpException;

use const SORT_ASC;
use const SORT_DESC;

final class UserStatSplatfestAction extends Action
{
    private User $user;
    private ?Region2 $region = null;

    public function run(): string
    {
        $this->initUser();

        $model = $this->processInput();
        $this->initRegion($model);

        if (!$this->region) {
            throw new LogicException();
        }

        return $this->controller->render('user-stat-splatfest', [
            'input' => $model,
            'region' => $this->region,
            'regions' => Region2::find()
                ->orderBy(['id' => SORT_ASC])
                ->all(),
            'splatfests' => Splatfest2::find()
                ->innerJoinWith(['splatfest2Regions'], false)
                ->andWhere(['splatfest2_region.region_id' => $this->region->id])
                ->orderBy(['splatfest2.term' => SORT_DESC])
                ->all(),
            'user' => $this->user,
        ]);
    }

    private function initUser(): void
    {
        $request = Yii::$app->request;
        $user = User::findOne(['screen_name' => (string)$request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app-show', 'Could not find user'));
        }
        $this->user = $user;
    }

    private function processInput(): UserStatSplatfest2Form
    {
        $request = Yii::$app->request;
        return UserStatSplatfest2Form::validateData(
            ['region' => $request->get('region')],
        );
    }

    private function initRegion(UserStatSplatfest2Form $model): void
    {
        if (
            $model->region !== null &&
            $model->region !== '' &&
            !$model->hasErrors()
        ) {
            if ($this->region = Region2::findOne(['key' => $model->region])) {
                return;
            }
        }

        $this->region = $this->user->guessedSplatfest2Region;
    }
}
