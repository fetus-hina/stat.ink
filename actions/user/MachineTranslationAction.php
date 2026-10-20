<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Override;
use Yii;
use app\models\MachineTranslationForm;
use yii\web\ViewAction as BaseAction;

class MachineTranslationAction extends BaseAction
{
    /**
     * @return array<string, mixed>
     */
    #[Override]
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

        Yii::$app->setEnabledMachineTranslation($form->direction === 'enable');

        return [
            'enabled' => $form->direction === 'enable',
        ];
    }

    private function makeValidationModel(): MachineTranslationForm
    {
        return MachineTranslationForm::validateData(['direction' => null]);
    }
}
