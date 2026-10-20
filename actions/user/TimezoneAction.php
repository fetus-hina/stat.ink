<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Override;
use Yii;
use app\models\UserTimezoneForm;
use yii\web\Cookie;
use yii\web\ViewAction as BaseAction;

use function time;

class TimezoneAction extends BaseAction
{
    private ?string $previousTimezone = null;

    #[Override]
    public function init(): void
    {
        $this->previousTimezone = Yii::$app->timeZone;
        parent::init();
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function run(): array
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';

        $form = $this->makeValidationModel();
        $form->timezone = Yii::$app->getRequest()->post('timezone');
        if (!$form->validate()) {
            $response->statusCode = 400;
            return ['errors' => $form->getErrors()];
        }

        $response->cookies->add(
            new Cookie([
                'name' => 'timezone',
                'value' => $form->timezone,
                'expire' => time() + 86400 * 366,
            ]),
        );

        return [
            'previous' => $this->previousTimezone,
            'next' => $form->timezone,
        ];
    }

    private function makeValidationModel(): UserTimezoneForm
    {
        return UserTimezoneForm::validateData(['timezone' => null]);
    }
}
