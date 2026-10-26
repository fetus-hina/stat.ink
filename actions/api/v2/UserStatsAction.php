<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\api\v2;

use Override;
use Yii;
use app\models\ApiV2UserStatsForm;
use app\models\User;
use app\models\UserStat2;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

class UserStatsAction extends Action
{
    #[Override]
    public function init(): void
    {
        parent::init();
        Yii::$app->response->format = 'json';
    }

    /**
     * @return array<string, mixed>
     */
    public function run(): array
    {
        $form = $this->processInput();
        if ($form->hasErrors()) {
            $resp = Yii::$app->response;
            $resp->format = 'json';
            $resp->statusCode = 400;
            return [
                'error' => $form->getErrors(),
            ];
        }

        $user = User::findOne(['screen_name' => $form->screen_name]);
        if (!$user) {
            throw new ServerErrorHttpException('input is valid but user does not exists');
        }

        $stats = $user->userStat2 ?: Yii::createObject(UserStat2::class);
        return $stats->toJsonArray();
    }

    protected function processInput(): ApiV2UserStatsForm
    {
        $get = Yii::$app->request->get();
        $input = [
            'screen_name' => ArrayHelper::getValue($get, 'screen_name'),
        ];

        return ApiV2UserStatsForm::validateData($input);
    }
}
