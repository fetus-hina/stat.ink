<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v2;

use Yii;
use app\models\User;
use app\models\UserStat2;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

class UserStatsAction extends Action
{
    public function init()
    {
        parent::init();
        Yii::$app->response->format = 'json';
    }

    public function run()
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

    protected function processInput(): DynamicModel
    {
        $get = Yii::$app->request->get();
        $input = [
            'screen_name' => ArrayHelper::getValue($get, 'screen_name'),
        ];

        return DynamicModel::validateData($input, [
            [['screen_name'], 'required'],
            [['screen_name'], 'string'],
            [['screen_name'], 'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => 'screen_name',
            ],
        ]);
    }
}
