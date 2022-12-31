<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use app\models\Language;
use yii\base\DynamicModel;
use yii\web\Cookie;
use yii\web\ViewAction as BaseAction;

class LanguageAction extends BaseAction
{
    private $previousLanguage = null;

    public function init()
    {
        $this->previousLanguage = Yii::$app->language;
        Yii::$app->language = 'en-US';
        parent::init();
    }

    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';

        $form = $this->makeValidationModel();
        $form->language = Yii::$app->getRequest()->post('language');
        if (!$form->validate()) {
            $response->statusCode = 400;
            return ['errors' => $form->getErrors()];
        }

        $response->cookies->add(
            new Cookie([
                'name' => 'language',
                'value' => $form->language,
                'expire' => time() + 86400 * 366,
            ]),
        );

        return [
            'previous' => $this->previousLanguage,
            'next' => $form->language,
        ];
    }

    private function makeValidationModel()
    {
        $model = DynamicModel::validateData(
            ['language' => null],
            [
                [['language'], 'required'],
                [['language'], 'exist',
                    'targetClass' => Language::className(),
                    'targetAttribute' => 'lang'],
            ],
        );
        return $model;
    }
}
