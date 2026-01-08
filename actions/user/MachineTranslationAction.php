<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use yii\base\DynamicModel;
use yii\web\ViewAction as BaseAction;

class MachineTranslationAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';

        $form = $this->makeValidationModel();
        $form->direction = Yii::$app->getRequest()->post('direction');
        if (!$form->validate()) {
            $response->statusCode = 400;
            return [
                'errors' => $form->getErrors(),
            ];
        }

        Yii::$app->setEnabledMachineTranslation($form->direction === 'enable');

        return [
            'enabled' => $form->direction === 'enable',
        ];
    }

    private function makeValidationModel(): DynamicModel
    {
        return DynamicModel::validateData(
            ['direction' => null],
            [
                [['direction'], 'required'],
                [['direction'], 'in',
                    'range' => ['enable', 'disable'],
                    'strict' => true,
                ],
            ],
        );
    }
}
