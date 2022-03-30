<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\T;
use yii\base\Action;
use yii\base\DynamicModel;

final class MachineTranslationAction extends Action
{
    public function run(): array
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

        T::webApplication(Yii::$app)
            ->setEnabledMachineTranslation($form->direction === 'enable');

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
            ]
        );
    }
}
