<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use app\models\Timezone;
use yii\base\DynamicModel;
use yii\web\Cookie;
use yii\web\ViewAction as BaseAction;

use function time;

class TimezoneAction extends BaseAction
{
    private $previousTimezone = null;

    public function init()
    {
        $this->previousTimezone = Yii::$app->timeZone;
        parent::init();
    }

    public function run()
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

    private function makeValidationModel()
    {
        return DynamicModel::validateData(
            ['timezone' => null],
            [
                [['timezone'], 'required'],
                [['timezone'], 'exist',
                    'targetClass' => Timezone::className(),
                    'targetAttribute' => 'identifier',
                ],
            ],
        );
    }
}
