<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v2;

use Yii;
use app\models\Region2;
use app\models\User;
use yii\base\DynamicModel;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction;

use const SORT_ASC;
use const SORT_DESC;

class UserStatSplatfestAction extends ViewAction
{
    private ?User $user = null;
    private ?Region2 $region = null;

    public function run()
    {
        $this->initUser();

        $model = $this->processInput();
        $this->initRegion($model);

        return $this->controller->render('user-stat-splatfest', [
            'input' => $model,
            'region' => $this->region,
            'regions' => Region2::find()
                ->orderBy(['id' => SORT_ASC])
                ->all(),
            'splatfests' => $this->region
                ->getFests()
                ->orderBy(['term' => SORT_DESC])
                ->all(),
            'user' => $this->user,
        ]);
    }

    private function initUser(): void
    {
        $request = Yii::$app->request;
        $this->user = User::findOne(['screen_name' => (string)$request->get('screen_name')]);
        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }
    }

    private function processInput(): DynamicModel
    {
        $request = Yii::$app->request;
        return DynamicModel::validateData(
            ['region' => $request->get('region')],
            [
                [['region'], 'default', 'value' => null],
                [['region'], 'string'],
                [['region'], 'exist',
                    'targetClass' => Region2::class,
                    'targetAttribute' => ['region' => 'key'],
                ],
            ]
        );
    }

    private function initRegion(DynamicModel $model): void
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
